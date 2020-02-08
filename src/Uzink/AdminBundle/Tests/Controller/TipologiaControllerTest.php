<?php

namespace Uzink\AdminBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class TipologiaControllerTest
 * @package Uzink\AdminBundle\Tests\Controller
 */
class TipologiaControllerTest extends WebTestCase
{
    /**
     * @var \Symfony\Bundle\FrameworkBundle\Client
     */
    private $client;

    /**
     * Set Up Before Class
     */
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
    }

    /**
     * Set Up
     */
    protected function setUp()
    {
        $this->client = static::createClient();
    }

    /**
     * Tear Down
     */
    protected function tearDown()
    {
        static::$kernel->getContainer()->get('doctrine')->getConnection()->close();

        parent::tearDown();
    }

    /**
     * Tear Down After Class
     */
    public static function tearDownAfterClass()
    {
        parent::tearDownAfterClass();
    }

    public function testAccessPageTipologyListWithLoggedUser()
    {
        // method
        $crawler = $this->client->request(
            'GET',
            '/admin/tipologia/list/1',
            array(),
            array(),
            array(
                'PHP_AUTH_USER' => 'admin01',
                'PHP_AUTH_PW'   => 'admin01'
            )
        );

        // statusCode:200, Success:OK
        $this->assertTrue($this->client->getResponse()->isSuccessful());
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        // data in html
        $this->assertCount(1, $crawler->filter('h1:contains("Tipologías")'));
    }

    public function testAccessPageNewTipologyWithLoggedUser()
    {
        // method
        $crawler = $this->client->request(
            'GET',
            '/admin/tipologia/new',
            array(),
            array(),
            array(
                'PHP_AUTH_USER' => 'admin01',
                'PHP_AUTH_PW'   => 'admin01'
            )
        );

        // statusCode:200, Success:OK
        $this->assertTrue($this->client->getResponse()->isSuccessful());
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        // data in html
        $this->assertCount(1, $crawler->filter('h1:contains("Creación de Tipología")'));
    }
}
