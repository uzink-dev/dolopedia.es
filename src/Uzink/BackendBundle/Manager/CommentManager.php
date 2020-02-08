<?php
namespace Uzink\BackendBundle\Manager;

use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Acl\Domain\UserSecurityIdentity;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;

class CommentManager extends Manager
{
    public function __construct($class, $formClass) {
        $this->class = $class;
        $this->formClass = $formClass;
    }
}