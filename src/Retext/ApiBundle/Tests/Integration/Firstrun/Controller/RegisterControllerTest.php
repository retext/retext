<?php

namespace Retext\ApiBundle\Tests\Integration\Firstrun\Controller;

use Retext\ApiBundle\Tests\Integration\ApiClient;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Testet die Schnittstellen zum Registrieren der Benutzer
 *
 * @author Markus Tacker <m@tckr.cc>
 */
class RegisterControllerTest extends WebTestCase
{
    /**
     * @var \Retext\ApiBundle\Tests\Integration\ApiClient
     */
    private $client;

    public function setUp()
    {
        $this->client = new ApiClient(static::createClient());
    }

    /**
     * @group integration
     * @group firstrun
     */
    public function testRegister()
    {
        $this->client->CREATE('/user', array('email' => 'phpunit@retext.it'));
    }

    /**
     * @depends testRegister
     * @group integration
     * @group firstrun
     */
    public function testLogin()
    {
        $user = $this->client->POST('/login', array('email' => 'phpunit@retext.it', 'password' => 'phpunit@retext.it'));

        $this->assertObjectHasAttribute('email', $user);
        $this->assertObjectNotHasAttribute('password', $user);
        $this->assertEquals('phpunit@retext.it', $user->email);

        $cookies = $this->client->getResponse()->getHeaderCookies();
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
        $this->client->doRequest('POST', '/login', array('email' => 'phpunit@retext.it', 'password' => 'invalid'), 403);
    }

    /**
     * @depends testLogin
     * @group integration
     * @group firstrun
     */
    public function testAuthStatus()
    {
        $this->client->POST('/login', array('email' => 'phpunit@retext.it', 'password' => 'phpunit@retext.it'));
        $auth = $this->client->GET('/auth');
        $cookies = $this->client->getResponse()->getHeaderCookies();
        $this->assertEquals(1, count($cookies));
        $this->assertEquals('MOCKSESSID', $cookies[0]->getName());
        $this->assertFalse($cookies[0]->getValue() == "");
        $this->assertObjectHasAttribute('authorized', $auth);
        $this->assertTrue($auth->authorized);
    }

    /**
     * @depends testLogin
     * @group integration
     * @group firstrun
     */
    public function testLogout()
    {
        $this->client->POST('/login', array('email' => 'phpunit@retext.it', 'password' => 'phpunit@retext.it'));
        $this->client->doRequest('POST', '/logout', null, 204);
        $auth = $this->client->GET('/auth');
        $this->assertObjectHasAttribute('authorized', $auth);
        $this->assertFalse($auth->authorized);
    }
}
