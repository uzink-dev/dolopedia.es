<?php

namespace Uzink\BackendBundle\Security\Permission;

use Symfony\Component\Security\Acl\Permission\BasicPermissionMap;

class PermissionMap extends BasicPermissionMap {
    const PERMISSION_CONTENT   = 'CONTENT';

    public function __construct()
    {
        parent::__construct();
        $this->map[self::PERMISSION_VIEW] = array(
            MaskBuilder::MASK_VIEW,
            MaskBuilder::MASK_CONTENT,
            MaskBuilder::MASK_EDIT,
            MaskBuilder::MASK_OPERATOR,
            MaskBuilder::MASK_MASTER,
            MaskBuilder::MASK_OWNER,
        );
        $this->map[self::PERMISSION_CONTENT] = array(
            MaskBuilder::MASK_CONTENT,
            MaskBuilder::MASK_EDIT,
            MaskBuilder::MASK_OPERATOR,
            MaskBuilder::MASK_MASTER,
            MaskBuilder::MASK_OWNER,
        );
    }
}