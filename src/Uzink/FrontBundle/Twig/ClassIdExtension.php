<?php

namespace Uzink\FrontBundle\Twig;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Acl\Domain\ObjectIdentity;

class ClassIdExtension extends \Twig_Extension
{
    /** @var EntityManager */
    private $em;

    public function getFunctions()
    {
        return array(
            'classId' => new \Twig_SimpleFunction('classId', array($this, 'classId')),
            'categoryFromId' => new \Twig_SimpleFunction('categoryFromId', array($this, 'categoryFromId'))
        );
    }

    public function classId($class)
    {
        if (is_object($class)) $class = get_class($class);

        return new ObjectIdentity('class', $class);
    }

    public function categoryFromId($id)
    {
        $repo = $this->em->getRepository('BackendBundle:Category');

        return $repo->find($id);
    }

    public function getName()
    {
        return 'classidextension';
    }

    public function setDependencies($em)
    {
        $this->em = $em;
    }
}