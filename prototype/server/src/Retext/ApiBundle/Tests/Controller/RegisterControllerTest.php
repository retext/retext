<?php

namespace Retext\ApiBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RegisterControllerTest extends WebTestCase
{
    public function testRegister()
    {
        $client = static::createClient();
        $client->request('PUT', '/api/user', array('email' => 'phpunit@retext.it'));
        $this->assertEquals(201, $client->getResponse()->getStatusCode());
        $this->assertNotEmpty($client->getResponse()->headers->get('Location'));
    }

    /**
     * @depend testRegister
     */
    public function testLogin()
    {
        $client = static::createClient();
        $client->request('POST', '/api/login', array('email' => 'phpunit@retext.it', 'password' => 'phpunit@retext.it'));
        $this->assertEquals(204, $client->getResponse()->getStatusCode());
    }

    /**
     * @depend testRegister
     */
    public function testBadLogin()
    {
        $client = static::createClient();
        $client->request('POST', '/api/login', array('email' => 'phpunit@retext.it', 'password' => 'invalid'));
        $this->assertEquals(403, $client->getResponse()->getStatusCode());
    }
}
