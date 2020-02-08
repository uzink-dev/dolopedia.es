<?php

namespace Uzink\BackendBundle\DataFixtures\ORM;

use Uzink\BackendBundle\DataFixtures\Model\ArrayFixture;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ArticleFixtures extends ArrayFixture implements ContainerAwareInterface
{
    protected $order = 5;
    
    protected $dataClassName = 'Uzink\BackendBundle\Entity\Article';
    
    protected $data;
    
    protected $container;
    
    public function loadData() {
        $data = array();
        
        for ($index = 1; $index < 150; $index++) {
            $category = floor($index / 10) + 1;
            $leader = floor($index / 10) + 1;
            $data[] = array(
                'ref' => '_ref_article_'.$index,
                'category' => '_ref_category_'.$category,
                'type' => '1',
                'title' => 'Titulo'.$index,
                'content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse facilisis',
                'leader' => '_ref_usuario_'.$leader
            );
        }
        
        $this->data = $data;
    }
    
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
        $this->loadData();
    }    
        

}
