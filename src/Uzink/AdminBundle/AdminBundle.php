<?php

namespace Uzink\AdminBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class AdminBundle
 * @package Uzink\AdminBundle
 */
class AdminBundle extends Bundle
{
    public function getParent()
    {
        return 'FOSUserBundle';
    }
}
