<?php

namespace Uzink\GeneratorBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\Console\Application;
use Uzink\GeneratorBundle\Generator\DoctrineCrudGenerator;
use Symfony\Component\Filesystem\Filesystem;

class GeneratorBundle extends Bundle
{
    public function registerCommands(Application $application)
    {
        parent::registerCommands($application);

        $crudCommand = $application->get('doctrine:generate:crud');

        $filesystem = new Filesystem(dirname(__FILE__) . '/Resources/skeleton/crud');
        $generator = new DoctrineCrudGenerator($filesystem);

        $crudCommand->setGenerator($generator);
    }
}
