<?php

namespace Retext\ApiBundle\Tests\Integration\Firstrun\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Simpler Test für die Dummy-Schnittstelle
 *
 * @author Markus Tacker <m@tckr.cc>
 */
class DummyControllerTest extends WebTestCase
{
    public function testHello()
    {
        $client = static::createClient();
        $client->request('GET', '/api/hello/Markus', array(), array(), array('HTTP_ACCEPT' => 'application/json'));
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals("application/json", $client->getResponse()->getHeader('Content-Type'));
        $response = json_decode($client->getResponse()->getContent());
        $this->assertObjectHasAttribute('name', $response);
        $this->assertEquals('Markus', $response->name);
    }
}
