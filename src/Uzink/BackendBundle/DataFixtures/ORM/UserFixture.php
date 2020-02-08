<?php

namespace Uzink\BackendBundle\DataFixtures\ORM;

use Uzink\BackendBundle\DataFixtures\Model\ArrayFixture;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class UserFixtures extends ArrayFixture implements ContainerAwareInterface
{
    protected $order = 2;
    
    protected $dataClassName = 'Uzink\BackendBundle\Entity\User';
    
    protected $data;
    
    protected $container;
    
    public function loadData() {
        $data = array();
        
        for ($index = 1; $index < 30; $index++) {
            $center = floor($index / 10) + 1;
            $data[] = array(
                'ref' => '_ref_usuario_'.$index,
                'username' => 'Usuario'.$index,
                'plainPassword' => '12345',
                'password' => '12345',
                'email' => 'usuario'.$index.'@prueba.com',
                'enabled' => true,
                'workplace' => '_ref_center_'.$center,
                'name' => 'Usuario'.$index,
                'surname1' => 'Apellido1'.$index,
                'surname2' => 'Apellido2'.$index
            );
        }
        
        $data[] = array(
            'ref' => '_ref_admin_1',
            'username' => 'Admin1',
            'plainPassword' => '12345',
            'password' => '12345',
            'email' => 'admin1@prueba.com',
            'enabled' => true,
            'workplace' => '_ref_center_1',
            'name' => 'Adminsitrador1',
            'surname1' => 'Apellido1',
            'surname2' => 'Apellido2',
            'roles' => array('ROLE_SUPERADMIN')
        );
        
        $this->data = $data;
    }
    
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
        $this->loadData();
    }    
        

}
