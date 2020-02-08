<?php
namespace Uzink\BackendBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Uzink\BackendBundle\Entity\Category;

class CategoryCommand extends ContainerAwareCommand
{
    private $em;
    private $repo;
    private $output;

    protected function configure()
    {
        $this
            ->setName('category:set:tree')
            ->setDescription('Set Tree Categories')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $this->repo = $this->em->getRepository('BackendBundle:Category');
        $this->output = $output;

        $output->writeln('Leveling...');
        $rootNode = $this->repo->findBy(['title' => 'Dolopedia']);
        $this->updateNodes($rootNode);
        $this->em->flush();

        $output->writeln('Recovering...');

        $this->repo->recover();
        $this->em->flush();

        $this->em->clear();

        $output->writeln('Verifying...');

        var_dump($this->repo->verify());
        $this->em->clear();

/*        $rootCategories = $repo->findByParentCategory(null);

        foreach($rootCategories as $category) {
            $category->setRoot()
        }

        echo('Cuenta ' . count($rootCategories));*/

/*        $category = $repo->find(78);
        $parentCategory = $repo->find(82);
        $category->setParent($parentCategory);
        $em->persist($category);
        $em->flush();*/



/*        $categories = $repo->findAll();
        foreach($categories as $category) {
            $output->writeln($category->getTitle());
            $category->setParent($category->getParentCategory());
            $em->persist($category);
        }

        $em->flush();*/

/*
        $this->updateNodes($rootNode);

        $em->flush();





        $htmlTree = $repo->childrenHierarchy(
            null,
            false,
            array(
                'decorate' => true,
                'representationField' => 'slug',
                'html' => false
            )
        );

        $output->writeln($htmlTree);*/

        $output->writeln('Categories setted');
    }

    private function updateNodes($nodes, $level = 0) {
        foreach ($nodes as $node) {
            $node->setLvl($level);
            $nodes = $this->repo->children($node, true);
            $this->em->persist($node);
            $this->updateNodes($nodes, $level +1);
        }
    }
}