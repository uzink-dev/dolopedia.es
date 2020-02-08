<?php

namespace Uzink\BackendBundle\DataFixtures\ORM;

use Uzink\BackendBundle\DataFixtures\Model\ArrayFixture;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class CenterFixtures extends ArrayFixture implements ContainerAwareInterface
{
    protected $order = 1;
    
    protected $dataClassName = 'Uzink\BackendBundle\Entity\Center';
    
    protected $data;
    
    protected $container;
    
    public function loadData() {
        $data = array(
            1 => array(
                'ref' => '_ref_center_1',
                'title' => 'Centro de trabajo 1',
                'link' => 'http://www.centro1.com',
                'type' => 'Tipo 1'
            ),
            2 => array(
                'ref' => '_ref_center_2',
                'title' => 'Centro de trabajo 2',
                'link' => 'http://www.centro2.com',
                'type' => 'Tipo 1'
            ),
            3 => array(
                'ref' => '_ref_center_3',
                'title' => 'Centro de trabajo 3',
                'link' => 'http://www.centro3.com',
                'type' => 'Tipo 1'
            ),
            4 => array(
                'ref' => '_ref_center_4',
                'title' => 'Centro de trabajo 4',
                'link' => 'http://www.centro4.com',
                'type' => 'Tipo 1'
            ),
            5 => array(
                'ref' => '_ref_center_5',
                'title' => 'Centro de trabajo 5',
                'link' => 'http://www.centro5.com',
                'type' => 'Tipo 1'
            ),           
            6 => array(
                'ref' => '_ref_center_6',
                'title' => 'Centro de trabajo 6',
                'link' => 'http://www.centro6.com',
                'type' => 'Tipo 1'
            )
        );
        $this->data = $data;
    }
    
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
        $this->loadData();
    }    
        

}
