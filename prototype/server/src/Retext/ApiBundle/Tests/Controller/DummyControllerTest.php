<?php

namespace Retext\ApiBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DummyControllerTest extends WebTestCase
{
    public function testHello()
    {
        $client = static::createClient();
        $client->request('GET', '/api/hello/Markus');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals("application/json", $client->getResponse()->headers->get('Content-Type'));
        $response = json_decode($client->getResponse()->getContent());
        $this->assertObjectHasAttribute('name', $response);
        $this->assertEquals('Markus', $response->name);
    }
}
