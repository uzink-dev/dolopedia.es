<?php

namespace Uzink\BackendBundle\Security\Permission;

use Symfony\Component\Security\Acl\Permission\MaskBuilder as BaseMaskBuilder;

class MaskBuilder extends BaseMaskBuilder {
    const MASK_CONTENT = 256;
    const CODE_CONTENT = 'A';
}
