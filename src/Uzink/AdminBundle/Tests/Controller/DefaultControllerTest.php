<?php

namespace Uzink\AdminBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class DefaultControllerTest
 * @package Uzink\AdminBundle\Tests\Controller
 */
class DefaultControllerTest extends WebTestCase
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

    public function providerMethodAdminAreaWithoutLoggedUser()
    {
        return array(
            array('HEAD', 401, null), // Client Error:Unauthorized
            array('GET',  401, null), // Client Error:Unauthorized
            array('POST', 405, null), // Client Error:Method not Allowed
        );
    }

    /**
     * @dataProvider providerMethodAdminAreaWithoutLoggedUser
     */
    public function testAccessPageAdminAreaWithoutLoggedUser($method, $statusCode, $target)
    {
        // method
        $this->client->request($method, '/admin');

        // statusCode
        $this->assertEquals($statusCode, $this->client->getResponse()->getStatusCode());

        // check target if redirect
        if(null !== $target) {
            $this->assertTrue($this->client->getResponse()->isRedirect($target));
        }
    }

    public function providerMethodAdminAreaWithLoggedUser()
    {
        return array(
            array('HEAD', 301, 'http://localhost/admin/'), // Client Error:Moved permanently
            array('GET',  301, 'http://localhost/admin/'), // Client Error:Moved permanently
            array('POST', 405, null),                      // Client Error:Method not Allowed
        );
    }

    /**
     * @dataProvider providerMethodAdminAreaWithLoggedUser
     */
    public function testAccessPageAdminAreaWithLoggedUser($method, $statusCode, $target)
    {
        // method
        $this->client->request(
            $method,
            '/admin',
            array(),
            array(),
            array(
                'PHP_AUTH_USER' => 'admin01',
                'PHP_AUTH_PW'   => 'admin01'
            )
        );

        // statusCode
        $this->assertEquals($statusCode, $this->client->getResponse()->getStatusCode());

        // check target if redirect
        if(null !== $target) {
            $this->assertTrue($this->client->getResponse()->isRedirect($target));
        }
    }

    public function providerMethodHomepageWithoutLoggedUser()
    {
        return array(
            array('HEAD', 401, null), // Client Error:Unauthorized
            array('GET',  401, null), // Client Error:Unauthorized
            array('POST', 405, null), // Client Error:Method not Allowed
        );
    }

    /**
     * @dataProvider providerMethodHomepageWithoutLoggedUser
     */
    public function testAccessPageHomepageWithoutLoggedUser($method, $statusCode, $target)
    {
        // method
        $this->client->request($method, '/admin/');

        // statusCode
        $this->assertEquals($statusCode, $this->client->getResponse()->getStatusCode());

        // check target if redirect
        if(null !== $target) {
            $this->assertTrue($this->client->getResponse()->isRedirect($target));
        }
    }

    public function providerMethodHomepageWithLoggedUser()
    {
        return array(
            array('HEAD', 200, null), // Success:OK
            array('GET',  200, null), // Success:OK
            array('POST', 405, null), // Client Error:Method not Allowed
        );
    }

    /**
     * @dataProvider providerMethodHomepageWithLoggedUser
     */
    public function testAccessPageHomepageWithLoggedUser($method, $statusCode, $target)
    {
        // method
        $this->client->request(
            $method,
            '/admin/',
            array(),
            array(),
            array(
                'PHP_AUTH_USER' => 'admin01',
                'PHP_AUTH_PW'   => 'admin01'
            )
        );

        // statusCode
        $this->assertEquals($statusCode, $this->client->getResponse()->getStatusCode());

        // check target if redirect
        if(null !== $target) {
            $this->assertTrue($this->client->getResponse()->isRedirect($target));
        }
    }

    public function testPageHomepageWithLoggedUser()
    {
        // method
        $crawler = $this->client->request(
            'GET',
            '/admin/',
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
        $this->assertCount(1, $crawler->filter('html:contains("Administración")'));
    }
}
