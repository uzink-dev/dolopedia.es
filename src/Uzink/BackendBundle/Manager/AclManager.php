<?php

namespace Uzink\BackendBundle\Manager;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Acl\Domain\Entry;
use Symfony\Component\Security\Acl\Domain\Acl;
use Symfony\Component\Security\Acl\Dbal\MutableAclProvider;
use Symfony\Component\Security\Acl\Domain\RoleSecurityIdentity;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Acl\Model\AclProviderInterface;
use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Acl\Domain\UserSecurityIdentity;
use Uzink\BackendBundle\Security\Permission\MaskBuilder;
use Uzink\BackendBundle\Entity\User;

/**
 * Easily work with Symfony ACL
 *
 * This class abstracts some of the ACL layer and
 * gives you very easy "Grant" and "Revoke" methods
 * which will update existing ACL's and create new ones
 * when required
 *
 * @author CodinNinja
 */
class AclManager {

    protected $provider;

    protected $context;

    /**
     * Constructor
     *
     * @param AclProviderInterface $provider
     * @param SecurityContextInterface $context
     */
    public function __construct(AclProviderInterface $provider, SecurityContextInterface $context) {
        $this->provider = $provider;
        $this->context = $context;
    }

    /**
     * Grant a permission
     *
     * @param mixed $entity The DomainObject to add the permissions for
     * @param array $permissions The initial mask
     * @param string|User $identity
     * @throws \Exception
     * @return Object The original Entity
     */
    public function grant($entity, $permissions, $identity = null) {
        $acl = $this->getAcl($entity);

        $securityIdentity = $this->getSecurityIdentity($identity);
        $mask = $this->getMask($permissions);


        $this->addMask($securityIdentity, $mask, $acl);

        return $entity;
    }

    /**
     * Get or create an ACL object
     *
     * @param object $entity The Domain Object to get the ACL for
     *
     * @return Acl The found / craeted ACL
     */
    protected function getAcl($entity) {
        // creating the ACL
        $aclProvider = $this->provider;
        $objectIdentity = ObjectIdentity::fromDomainObject($entity);
        try {
            $acl = $aclProvider->createAcl($objectIdentity);
        }catch(\Exception $e) {
            $acl = $aclProvider->findAcl($objectIdentity);
        }

        return $acl;
    }

    /**
     * @param null $identity
     * @return RoleSecurityIdentity|UserSecurityIdentity
     * @throws \Exception
     */
    protected function getSecurityIdentity($identity = null) {
        if (!$identity) {
            $securityContext = $this->context;
            $user = $securityContext->getToken()->getUser();
            $securityIdentity = UserSecurityIdentity::fromAccount($user);
        } elseif ($identity instanceof User) {
            $securityIdentity = UserSecurityIdentity::fromAccount($identity);
        } elseif (is_string($identity)) {
            $securityIdentity = new RoleSecurityIdentity($identity);
        } else {
            throw new \Exception('Identity of type ' . get_class($identity) . ' are not allowed');
        }

        return $securityIdentity;
    }

    /**
     * @param array $permissions
     * @return int
     */
    protected function getMask($permissions) {
        $builder = new MaskBuilder();

        foreach ($permissions as $permission) {
            $builder->add($permission);
        }

        $mask = $builder->get();
        return $mask;
    }

    public function clean($entity, $users = null)
    {
        $acl = $this->getAcl($entity);
        $aces = $acl->getObjectAces();

        $securityIdentities = new ArrayCollection();
        if ($users) {
            foreach ($users as $user) {
                $securityIdentities->add($this->getSecurityIdentity($user));
            }

            for ($i=(count($aces)-1); $i>=0; $i--) {
                if(!$securityIdentities->contains($aces[$i])) {
                    $acl->deleteObjectAce($i);
                }
            }
        } else {
            for ($i=(count($aces)-1); $i>=0; $i--) {
                $acl->deleteObjectAce($i);
            }
        }

        $this->provider->updateAcl($acl);

        return $this;
    }

    /**
     * Revoke a permission
     *
     * <pre>
     *     $manager->revoke($myDomainObject, 'delete'); // Remove "delete" permission for the $myDomainObject
     * </pre>
     *
     * @param mixed $entity The DomainObject that we are revoking the permission for
     * @param array $permissions The mask to revoke
     * @param null| $identity
     * @return \Uzink\BackendBundle\Manager\AclManager Reference to $this for fluent interface
     */
    public function revoke($entity, $permissions, $identity = null) {
        $acl = $this->getAcl($entity);
        $aces = $acl->getObjectAces();

        $securityIdentity = $this->getSecurityIdentity($identity);
        $mask = $this->getMask($permissions);

        foreach($aces as $i => $ace) {
            if($securityIdentity->equals($ace->getSecurityIdentity())) {
                $this->revokeMask($i, $acl, $ace, $mask);
            }
        }

        $this->provider->updateAcl($acl);

        return $this;
    }

    /**
     * Remove a mask
     *
     * @param $index
     * @param Acl $acl The ACL to update
     * @param Entry $ace The ACE to remove the mask from
     * @param mixed $mask The mask to remove
     *
     * @return \Uzink\BackendBundle\Manager\AclManager Reference to $this for fluent interface
     */
    protected function revokeMask($index, Acl $acl, Entry $ace, $mask) {
        $acl->deleteObjectAce($index);
        //$acl->updateObjectAce($index, $ace->getMask() & ~$mask);

        return $this;
    }

    /**
     * Add a mask
     *
     * @param RoleSecurityIdentity|UserSecurityIdentity $securityIdentity The ACE to add
     * @param integer|string $mask The initial mask to set
     * @param ACL $acl The ACL to update
     *
     * @return \Uzink\BackendBundle\Manager\AclManager Reference to $this for fluent interface
     */
    protected function addMask($securityIdentity, $mask, $acl) {
        $updated = false;
        foreach($acl->getObjectAces() as $index=>$ace)
        {
            if($ace->getSecurityIdentity() == $securityIdentity)
            {
                $acl->updateObjectAce($index, $mask, null);
                $updated = true;
            }
        }
        if(!$updated)
        {
            $acl->insertObjectAce($securityIdentity, $mask);
        }
        $this->provider->updateAcl($acl);
        return $this;
    }
}