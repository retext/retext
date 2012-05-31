<?php

namespace Retext\ApiBundle\Tests\Integration\Secondrun\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ContainerControllerTest extends WebTestCase
{
    /**
     * @var \Symfony\Bundle\FrameworkBundle\Client
     */
    private $client;

    private $project;

    public function setUp()
    {
        $this->client = static::createClient();
        $this->client->request('POST', '/api/user', array(), array(), array('HTTP_ACCEPT' => 'application/json', 'HTTP_CONTENT_TYPE' => 'application/json'), json_encode(array('email' => 'phpunit+container@retext.it')));
        $this->client->request('POST', '/api/login', array(), array(), array('HTTP_ACCEPT' => 'application/json', 'HTTP_CONTENT_TYPE' => 'application/json'), json_encode(array('email' => 'phpunit+container@retext.it', 'password' => 'phpunit+container@retext.it')));
        $this->client->request('POST', '/api/project', array(), array(), array('HTTP_ACCEPT' => 'application/json', 'HTTP_CONTENT_TYPE' => 'application/json'), json_encode(array('name' => 'Container-Test-Project')));
        $this->project = json_decode($this->client->getResponse()->getContent());
    }

    /**
     * @group secondrun
     * @group integration
     */
    public function testCreateContainer()
    {
        $this->client->request('POST', $this->project->{'@subject'} . '/container', array(), array(), array('HTTP_ACCEPT' => 'application/json', 'HTTP_CONTENT_TYPE' => 'application/json'), json_encode(array('name' => 'Container 1')));
        $this->client->request('POST', $this->project->{'@subject'} . '/container', array(), array(), array('HTTP_ACCEPT' => 'application/json', 'HTTP_CONTENT_TYPE' => 'application/json'), json_encode(array('name' => 'Container 2')));
        $this->assertEquals(201, $this->client->getResponse()->getStatusCode());
        $this->assertNotEmpty($this->client->getResponse()->getHeader('Location'));
        $container = json_decode($this->client->getResponse()->getContent());
        $this->assertEquals($this->client->getResponse()->getHeader('Location'), $container->{'@subject'});
        $this->checkContainer($container);

        $this->client->request('GET', $this->project->{'@subject'} . '/container', array(), array(), array('HTTP_ACCEPT' => 'application/json', 'HTTP_CONTENT_TYPE' => 'application/json'));
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $container = json_decode($this->client->getResponse()->getContent());
        $this->assertInternalType('array', $container);
        $this->assertEquals(2, count($container));
        foreach ($container as $k => $c) {
            $this->checkContainer($c);
            $this->assertEquals($k + 1, $c->order, 'Reihenfolge sollte ' . ($k + 1) . ' sein.');
            $this->assertEquals('Container ' . ($k + 1), $c->name, 'Name sollte Container ' . ($k + 1) . ' sein.');
        }
        return $container;
    }

    private function checkContainer(\stdClass $container)
    {
        $this->assertObjectHasAttribute('@context', $container);
        $this->assertEquals('http://jsonld.retext.it/Container', $container->{'@context'});
        $this->assertObjectHasAttribute('@subject', $container);
        $this->assertNotNull($container->{'@subject'});
        // $this->assertObjectNotHasAttribute('parent', $container);
    }

    /**
     * @group secondrun
     * @group integration
     * @depends testCreateContainer
     * @return object
     */
    public function testCreateContainerWithoutName()
    {
        $this->client->request('POST', $this->project->{'@subject'} . '/container', array(), array(), array('HTTP_ACCEPT' => 'application/json', 'HTTP_CONTENT_TYPE' => 'application/json'));
        $this->assertEquals(201, $this->client->getResponse()->getStatusCode());
        $container = json_decode($this->client->getResponse()->getContent());
        $this->checkContainer($container);
        return $container;
    }

    /**
     * @group secondrun
     * @group integration
     * @depends testCreateContainerWithoutName
     * @return object
     */
    public function testUpdateContainerName(\stdClass $container)
    {
        $this->client->request('PUT', $container->{'@subject'}, array(), array(), array('HTTP_ACCEPT' => 'application/json', 'HTTP_CONTENT_TYPE' => 'application/json'), json_encode(array('name' => 'Flummi')));
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $container = json_decode($this->client->getResponse()->getContent());
        $this->checkContainer($container);
        $this->assertEquals('Flummi', $container->name);
    }

    /**
     * @group secondrun
     * @group integration
     * @depends testCreateContainer
     * @return object
     */
    public function testFlipContainerOrder(array $container)
    {
        $this->client->request('PUT', $container[1]->{'@subject'}, array(), array(), array('HTTP_ACCEPT' => 'application/json', 'HTTP_CONTENT_TYPE' => 'application/json'), json_encode(array('order' => 1)));
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $c2 = json_decode($this->client->getResponse()->getContent());
        $this->checkContainer($c2);
        $this->assertEquals(1, $c2->order);

        $this->client->request('GET', $container[0]->{'@subject'}, array(), array(), array('HTTP_ACCEPT' => 'application/json', 'HTTP_CONTENT_TYPE' => 'application/json'));
        $c1 = json_decode($this->client->getResponse()->getContent());
        $this->checkContainer($c1);
        $this->assertEquals(2, $c1->order);
    }

}
