<?php

namespace Uzink\BackendBundle\DataFixtures\ORM;

use Uzink\BackendBundle\DataFixtures\Model\ArrayFixture;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class CategoryFixtures extends ArrayFixture implements ContainerAwareInterface
{
    protected $order = 4;
    
    protected $dataClassName = 'Uzink\BackendBundle\Entity\Category';
    
    protected $data;
    
    protected $container;
    
    public function loadData() {
        $data = array();
        
        $img = 1;
        for ($index = 1; $index < 20; $index++) {
            $leader = floor($index / 10) + 1;
            if ($img > 9) $img = 1;
            $child = null;
            if ($index % 2) $child = '_ref_category'.$index - 1;
            if ($index % 3) $child = '_ref_category'.$index - 2;
            $data[] = array(
                'ref' => '_ref_category_'.$index,
                'title' => 'Titulo'.$index,
                'introduction' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse facilisis',
                'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse facilisis',
                'leader' => '_ref_admin_1',
                'image' => '_ref_img'.$img
            );
            $img++;
        }
        
        $this->data = $data;
    }
    
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
        $this->loadData();
    }    
        

}
