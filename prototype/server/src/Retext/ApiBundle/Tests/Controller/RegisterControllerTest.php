<?php

namespace Retext\ApiBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RegisterControllerTest extends WebTestCase
{
    /**
     * @group integration
     * @group firstrun
     */
    public function testRegister()
    {
        $client = static::createClient();
        $client->request('PUT', '/api/user', array('email' => 'phpunit@retext.it'));
        $this->assertEquals(201, $client->getResponse()->getStatusCode());
        $this->assertNotEmpty($client->getResponse()->headers->get('Location'));
    }

    /**
     * @depend testRegister
     * @group integration
     * @group firstrun
     */
    public function testLogin()
    {
        $client = static::createClient();
        $client->request('POST', '/api/login', array('email' => 'phpunit@retext.it', 'password' => 'phpunit@retext.it'));
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $response = json_decode($client->getResponse()->getContent());
        $this->assertObjectHasAttribute('email', $response);
        $this->assertObjectNotHasAttribute('password', $response);
        $this->assertEquals('phpunit@retext.it', $response->email);
        $cookies = $client->getResponse()->headers->getCookies();
        $this->assertEquals(1, count($cookies));
        $this->assertEquals('MOCKSESSID', $cookies[0]->getName());
        $this->assertFalse($cookies[0]->getValue() == "");
    }

    /**
     * @group integration
     * @group firstrun
     */
    public function testBadLogin()
    {
        $client = static::createClient();
        $client->request('POST', '/api/login', array('email' => 'phpunit@retext.it', 'password' => 'invalid'));
        $this->assertEquals(403, $client->getResponse()->getStatusCode());
    }

    /**
     * @depend testLogin
     * @group integration
     * @group firstrun
     */
    public function testAuthStatus()
    {
        $client = static::createClient();
        $client->request('POST', '/api/login', array('email' => 'phpunit@retext.it', 'password' => 'phpunit@retext.it'));
        $client->request('GET', '/api/auth');
        $this->assertEquals(204, $client->getResponse()->getStatusCode());
        $cookies = $client->getResponse()->headers->getCookies();
        $this->assertEquals(1, count($cookies));
        $this->assertEquals('MOCKSESSID', $cookies[0]->getName());
        $this->assertFalse($cookies[0]->getValue() == "");
    }

    /**
     * @depend testLogin
     * @group integration
     * @group firstrun
     */
    public function testLogout()
    {
        $client = static::createClient();
        $client->request('POST', '/api/login', array('email' => 'phpunit@retext.it', 'password' => 'phpunit@retext.it'));
        $client->request('POST', '/api/logout');
        $this->assertEquals(204, $client->getResponse()->getStatusCode());
        $client->request('GET', '/api/auth');
        $this->assertEquals(403, $client->getResponse()->getStatusCode());
    }
}
