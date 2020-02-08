<?php

namespace Uzink\Shtumi\MyUsefulBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class MyShtumiUsefulBundle extends Bundle
{
    public function getParent()
    {
        return 'ShtumiUsefulBundle';
    }
}
