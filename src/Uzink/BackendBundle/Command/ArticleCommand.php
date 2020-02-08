<?php
namespace Uzink\BackendBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Uzink\BackendBundle\Entity\Article;
use Uzink\BackendBundle\Entity\Category;

class ArticleCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('article:set:supervisor')
            ->setDescription('Set Article supervisor')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $categoryRepo = $em->getRepository('BackendBundle:Category');
        $articleRepo = $em->getRepository('BackendBundle:Article');

        $categories = $categoryRepo->findAll();

        foreach($categories as $category) {
            $output->writeln('Categoría: ' . $category->getTitle());
            $articles = $category->getArticles();
            $categoryOwner = $category->getOwner();
            /** @var Article $article */
            foreach ($articles as $article) {
                $output->writeln('--Artículo: ' . $article->getTitle());
                if ($categoryOwner && $categoryOwner != $article->getSupervisor()) {
                    $article->setSupervisor($categoryOwner);
                    $em->persist($categoryOwner);
                }
            }
            $em->flush();
        }
    }
}