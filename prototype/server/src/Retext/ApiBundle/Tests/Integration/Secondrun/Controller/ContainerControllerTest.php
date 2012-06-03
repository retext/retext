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
        $this->client->request('POST', '/api/container', array(), array(), array('HTTP_ACCEPT' => 'application/json', 'HTTP_CONTENT_TYPE' => 'application/json'), json_encode(array('project' => $this->project->id, 'name' => 'Container 1')));
        $this->client->request('POST', '/api/container', array(), array(), array('HTTP_ACCEPT' => 'application/json', 'HTTP_CONTENT_TYPE' => 'application/json'), json_encode(array('project' => $this->project->id, 'name' => 'Container 2')));
        $this->client->request('POST', '/api/container', array(), array(), array('HTTP_ACCEPT' => 'application/json', 'HTTP_CONTENT_TYPE' => 'application/json'), json_encode(array('project' => $this->project->id, 'name' => 'Container 3')));
        $this->client->request('POST', '/api/container', array(), array(), array('HTTP_ACCEPT' => 'application/json', 'HTTP_CONTENT_TYPE' => 'application/json'), json_encode(array('project' => $this->project->id, 'name' => 'Container 4')));
        $this->client->request('POST', '/api/container', array(), array(), array('HTTP_ACCEPT' => 'application/json', 'HTTP_CONTENT_TYPE' => 'application/json'), json_encode(array('project' => $this->project->id, 'name' => 'Container 5')));
        $this->assertEquals(201, $this->client->getResponse()->getStatusCode());
        $this->assertNotEmpty($this->client->getResponse()->getHeader('Location'));
        $container = json_decode($this->client->getResponse()->getContent());
        $this->assertEquals($this->client->getResponse()->getHeader('Location'), $container->{'@subject'});
        $this->checkContainer($container);

        $this->client->request('GET', $this->getRelationHref($this->project, 'http://jsonld.retext.it/Container', true), array(), array(), array('HTTP_ACCEPT' => 'application/json'));

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $container = json_decode($this->client->getResponse()->getContent());
        $this->assertInternalType('array', $container);
        $this->assertEquals(5, count($container));
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
        $this->client->request('POST', '/api/container', array(), array(), array('HTTP_ACCEPT' => 'application/json', 'HTTP_CONTENT_TYPE' => 'application/json'), json_encode(array('project' => $this->project->id)));
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
    public function testContainerReOrder(array $container)
    {
        $this->client->request('PUT', $container[1]->{'@subject'}, array(), array(), array('HTTP_ACCEPT' => 'application/json', 'HTTP_CONTENT_TYPE' => 'application/json'), json_encode(array('order' => 3)));
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $c2 = json_decode($this->client->getResponse()->getContent());
        $this->checkContainer($c2);
        $this->assertEquals(3, $c2->order);

        $newOrder = array(
            0 => 1,
            2 => 2,
            1 => 3,
            3 => 4,
            4 => 5,
        );

        foreach ($newOrder as $k => $expextedOrder) {
            $this->client->request('GET', $container[$k]->{'@subject'}, array(), array(), array('HTTP_ACCEPT' => 'application/json'));
            $c = json_decode($this->client->getResponse()->getContent());
            $this->checkContainer($c);
            $this->assertEquals($expextedOrder, $c->order, 'Container ' . ($k + 1) . ' should have order ' . $expextedOrder);
        }

        return $container;
    }


    /**
     * @group secondrun
     * @group integration
     * @depends testContainerReOrder
     * @return object
     */
    public function testContainerDelete(array $container)
    {
        $this->client->request('DELETE', $container[2]->{'@subject'}, array(), array(), array('HTTP_ACCEPT' => 'application/json', 'HTTP_CONTENT_TYPE' => 'application/json'));

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $this->client->request('GET', $container[2]->{'@subject'}, array(), array(), array('HTTP_ACCEPT' => 'application/json'));
        $this->assertEquals(410, $this->client->getResponse()->getStatusCode());

        $project = $this->fetchRelation($container[2], 'http://jsonld.retext.it/Project');

        $this->client->request('GET', $this->getRelationHref($project, 'http://jsonld.retext.it/Container', true), array(), array(), array('HTTP_ACCEPT' => 'application/json'));
        $containerList = json_decode($this->client->getResponse()->getContent());
        $this->assertEquals(4, count($containerList), 'There should be only 4 containers now.');

    }

    private function getRelationHref($object, $context, $list = false)
    {
        if (!property_exists($object, '@relations')) $this->fail('No @relations in ' . print_r($object, true));
        foreach ($object->{'@relations'} as $relation) {
            if ($relation->relatedcontext == $context && $relation->list === $list) return $relation->href;
        }
        $this->fail('Could not find relation ' . $context . ' (list=' . var_export($list, true) . ' in ' . print_r($object, true));
    }

    /**
     * @param object $object
     * @param string $context
     * @return object
     */
    private function fetchRelation($object, $context)
    {
        $this->client->request('GET', $this->getRelationHref($object, $context), array(), array(), array('HTTP_ACCEPT' => 'application/json'));
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        return json_decode($this->client->getResponse()->getContent());
    }
}
