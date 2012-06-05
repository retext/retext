<?php

namespace Retext\ApiBundle\Tests\Integration\Secondrun\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ContainerControllerTest extends Base
{
    private $project;

    public function setUp()
    {
        parent::setUp();
        $this->client->request('POST', '/api/user', array(), array(), array('HTTP_ACCEPT' => 'application/json', 'HTTP_CONTENT_TYPE' => 'application/json'), json_encode(array('email' => 'phpunit+container@retext.it')));
        $this->client->request('POST', '/api/login', array(), array(), array('HTTP_ACCEPT' => 'application/json', 'HTTP_CONTENT_TYPE' => 'application/json'), json_encode(array('email' => 'phpunit+container@retext.it', 'password' => 'phpunit+container@retext.it')));
        $this->client->request('POST', '/api/project', array(), array(), array('HTTP_ACCEPT' => 'application/json', 'HTTP_CONTENT_TYPE' => 'application/json'), json_encode(array('name' => 'Container-Test-Project')));
        $this->project = json_decode($this->client->getResponse()->getContent());
    }

    /**
     * @group secondrun
     * @group integration
     * @return object
     */
    public function testRootContainerExists()
    {
        // There must be a root container
        $this->client->request('GET', $this->getRelationHref($this->project, 'http://jsonld.retext.it/Container', false, 'http://jsonld.retext.it/ontology/root'), array(), array(), array('HTTP_ACCEPT' => 'application/json'));
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $root = json_decode($this->client->getResponse()->getContent());
        $this->checkContainer($root);
        $this->assertTrue($root->isRootContainer);
        return $root;
    }

    /**
     * @group secondrun
     * @group integration
     * @return object
     * @depends testRootContainerExists
     * @param object $root
     */
    public function testRootContainerCannotBeDeleted(\stdClass $root)
    {
        $this->client->request('DELETE', $root->{'@subject'}, array(), array(), array('HTTP_ACCEPT' => 'application/json'));
        $this->assertEquals(403, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @group secondrun
     * @group integration
     * @depends testRootContainerExists
     */
    public function testCreateContainer(\stdClass $root)
    {
        $this->client->request('POST', '/api/container', array(), array(), array('HTTP_ACCEPT' => 'application/json', 'HTTP_CONTENT_TYPE' => 'application/json'), json_encode(array('parent' => $root->id, 'name' => 'Container 1')));
        $this->client->request('POST', '/api/container', array(), array(), array('HTTP_ACCEPT' => 'application/json', 'HTTP_CONTENT_TYPE' => 'application/json'), json_encode(array('parent' => $root->id, 'name' => 'Container 2')));
        $this->client->request('POST', '/api/container', array(), array(), array('HTTP_ACCEPT' => 'application/json', 'HTTP_CONTENT_TYPE' => 'application/json'), json_encode(array('parent' => $root->id, 'name' => 'Container 3')));
        $this->client->request('POST', '/api/container', array(), array(), array('HTTP_ACCEPT' => 'application/json', 'HTTP_CONTENT_TYPE' => 'application/json'), json_encode(array('parent' => $root->id, 'name' => 'Container 4')));
        $this->client->request('POST', '/api/container', array(), array(), array('HTTP_ACCEPT' => 'application/json', 'HTTP_CONTENT_TYPE' => 'application/json'), json_encode(array('parent' => $root->id, 'name' => 'Container 5')));
        $this->assertEquals(201, $this->client->getResponse()->getStatusCode());
        $this->assertNotEmpty($this->client->getResponse()->getHeader('Location'));
        $container = json_decode($this->client->getResponse()->getContent());
        $this->assertEquals($this->client->getResponse()->getHeader('Location'), $container->{'@subject'});
        $this->checkContainer($container);

        $this->client->request('GET', $this->getRelationHref($root, 'http://jsonld.retext.it/Container', true), array(), array(), array('HTTP_ACCEPT' => 'application/json'));

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $container = json_decode($this->client->getResponse()->getContent());
        $this->assertInternalType('array', $container);
        $this->assertEquals(5, count($container));
        foreach ($container as $k => $c) {
            $this->checkContainer($c);
            $this->assertEquals('Container ' . ($k + 1), $c->name, 'Name sollte Container ' . ($k + 1) . ' sein.');
        }
        return array($root, $container);
    }

    private function checkContainer(\stdClass $container)
    {
        $this->assertObjectHasAttribute('@context', $container);
        $this->assertEquals('http://jsonld.retext.it/Container', $container->{'@context'});
        $this->assertObjectHasAttribute('@subject', $container);
        $this->assertNotNull($container->{'@subject'});
        // $this->assertObjectNotHasAttribute('parent', $container);
        $this->assertObjectHasAttribute('project', $container);
        $this->assertNotNull($container->project);
        $this->assertInternalType('string', $container->project);
    }

    /**
     * @group secondrun
     * @group integration
     * @return object
     */
    public function testCreateContainerWithoutName()
    {
        $this->client->request('POST', '/api/project', array(), array(), array('HTTP_ACCEPT' => 'application/json', 'HTTP_CONTENT_TYPE' => 'application/json'), json_encode(array('name' => 'No-Name-Container-Test-Project')));
        $project = json_decode($this->client->getResponse()->getContent());
        $this->client->request('POST', '/api/container', array(), array(), array('HTTP_ACCEPT' => 'application/json', 'HTTP_CONTENT_TYPE' => 'application/json'), json_encode(array('parent' => $project->rootContainer)));
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
    public function testContainerReOrder($args)
    {
        list ($root, $container) = $args;
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

        return array($root, $container);
    }


    /**
     * @group secondrun
     * @group integration
     * @depends testContainerReOrder
     * @return object
     */
    public function testContainerDelete($args)
    {
        list ($root, $container) = $args;
        $this->client->request('DELETE', $container[2]->{'@subject'}, array(), array(), array('HTTP_ACCEPT' => 'application/json', 'HTTP_CONTENT_TYPE' => 'application/json'));
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $this->client->request('GET', $container[2]->{'@subject'}, array(), array(), array('HTTP_ACCEPT' => 'application/json'));
        $this->assertEquals(410, $this->client->getResponse()->getStatusCode());

        $this->client->request('GET', $this->getRelationHref($root, 'http://jsonld.retext.it/Container', true), array(), array(), array('HTTP_ACCEPT' => 'application/json'));
        $containerList = json_decode($this->client->getResponse()->getContent());

        $this->assertEquals(4, count($containerList), 'There should be only 4 containers now.');

    }

    /**
     * @group secondrun
     * @group integration
     */
    public function testHierarchyContainer()
    {
        // Create project
        $this->client->request('POST', '/api/project', array(), array(), array('HTTP_ACCEPT' => 'application/json', 'HTTP_CONTENT_TYPE' => 'application/json'), json_encode(array('name' => 'Hierarchy-Container-Test-Project')));
        $project = json_decode($this->client->getResponse()->getContent());
        $this->client->request('GET', $this->getRelationHref($project, 'http://jsonld.retext.it/Container', false, 'http://jsonld.retext.it/ontology/root'), array(), array(), array('HTTP_ACCEPT' => 'application/json'));
        $root = json_decode($this->client->getResponse()->getContent());

        // Create tree
        $this->client->request('POST', '/api/container', array(), array(), array('HTTP_ACCEPT' => 'application/json', 'HTTP_CONTENT_TYPE' => 'application/json'), json_encode(array('parent' => $project->rootContainer, 'name' => '1.1')));
        $l1_1 = json_decode($this->client->getResponse()->getContent());
        $this->client->request('POST', '/api/container', array(), array(), array('HTTP_ACCEPT' => 'application/json', 'HTTP_CONTENT_TYPE' => 'application/json'), json_encode(array('parent' => $project->rootContainer, 'name' => '1.2')));
        $l1_2 = json_decode($this->client->getResponse()->getContent());
        foreach (array($l1_1, $l1_2) as $c) {
            $this->checkContainer($c);
            $this->assertObjectHasAttribute('parent', $c);
            $this->assertEquals($c->parent, $root->id);
        }
        $this->client->request('POST', '/api/container', array(), array(), array('HTTP_ACCEPT' => 'application/json', 'HTTP_CONTENT_TYPE' => 'application/json'), json_encode(array('parent' => $l1_1->id, 'name' => '1.1.1')));
        $l1_1_1 = json_decode($this->client->getResponse()->getContent());
        $this->client->request('POST', '/api/container', array(), array(), array('HTTP_ACCEPT' => 'application/json', 'HTTP_CONTENT_TYPE' => 'application/json'), json_encode(array('parent' => $l1_1->id, 'name' => '1.1.2')));
        $l1_1_2 = json_decode($this->client->getResponse()->getContent());
        foreach (array($l1_1_1, $l1_1_2) as $c) {
            $this->checkContainer($c);
            $this->assertObjectHasAttribute('parent', $c);
            $this->assertEquals($c->parent, $l1_1->id);
        }

        // Prüfe root
        $this->client->request('GET', $this->getRelationHref($project, 'http://jsonld.retext.it/Container', false, 'http://jsonld.retext.it/ontology/root'), array(), array(), array('HTTP_ACCEPT' => 'application/json'));
        $root = json_decode($this->client->getResponse()->getContent());
        $this->assertInternalType('object', $root);
        $this->assertEquals(2, $root->childcount);

        // Prüfe Level 1
        $this->client->request('GET', $this->getRelationHref($root, 'http://jsonld.retext.it/Container', true), array(), array(), array('HTTP_ACCEPT' => 'application/json'));
        $rootChilds = json_decode($this->client->getResponse()->getContent());
        $this->assertInternalType('array', $rootChilds);
        $this->assertEquals(2, count($rootChilds));
        $this->assertEquals('1.1', $rootChilds[0]->name);
        $this->assertEquals(2, $rootChilds[0]->childcount);
        $this->assertEquals('1.2', $rootChilds[1]->name);
        $this->assertEquals(0, $rootChilds[1]->childcount);

        // Prüfe Level 2
        $this->client->request('GET', $this->getRelationHref($rootChilds[0], 'http://jsonld.retext.it/Container', true), array(), array(), array('HTTP_ACCEPT' => 'application/json'));
        $l1childs = json_decode($this->client->getResponse()->getContent());
        $this->assertInternalType('array', $l1childs);
        $this->assertEquals(2, count($l1childs));
        $this->assertEquals('1.1.1', $l1childs[0]->name);
        $this->assertEquals('1.1.2', $l1childs[1]->name);
        $this->assertEquals(0, $l1childs[0]->childcount);
        $this->assertEquals(0, $l1childs[1]->childcount);

        $this->client->request('GET', $this->getRelationHref($rootChilds[1], 'http://jsonld.retext.it/Container', true), array(), array(), array('HTTP_ACCEPT' => 'application/json'));
        $l2childs = json_decode($this->client->getResponse()->getContent());
        $this->assertInternalType('array', $l2childs);
        $this->assertEquals(0, count($l2childs));
    }

    /**
     * @group secondrun
     * @group integration
     */
    public function testBreadCrumb()
    {
        // Create project
        $this->client->request('POST', '/api/project', array(), array(), array('HTTP_ACCEPT' => 'application/json', 'HTTP_CONTENT_TYPE' => 'application/json'), json_encode(array('name' => 'Breadcrumb-Test-Project')));
        $project = json_decode($this->client->getResponse()->getContent());
        $this->client->request('GET', $this->getRelationHref($project, 'http://jsonld.retext.it/Container', false, 'http://jsonld.retext.it/ontology/root'), array(), array(), array('HTTP_ACCEPT' => 'application/json'));
        $root = json_decode($this->client->getResponse()->getContent());
        // Create tree
        $this->client->request('POST', '/api/container', array(), array(), array('HTTP_ACCEPT' => 'application/json', 'HTTP_CONTENT_TYPE' => 'application/json'), json_encode(array('parent' => $root->id, 'name' => 'Ebene 1')));
        $l1 = json_decode($this->client->getResponse()->getContent());
        $this->client->request('POST', '/api/container', array(), array(), array('HTTP_ACCEPT' => 'application/json', 'HTTP_CONTENT_TYPE' => 'application/json'), json_encode(array('parent' => $l1->id, 'name' => 'Ebene 2')));
        $l2 = json_decode($this->client->getResponse()->getContent());
        $this->client->request('POST', '/api/container', array(), array(), array('HTTP_ACCEPT' => 'application/json', 'HTTP_CONTENT_TYPE' => 'application/json'), json_encode(array('parent' => $l2->id, 'name' => 'Ebene 3')));
        $l3 = json_decode($this->client->getResponse()->getContent());
        $this->client->request('GET', $this->getRelationHref($l3, 'http://jsonld.retext.it/Breadcrumb', true), array(), array(), array('HTTP_ACCEPT' => 'application/json'));
        $breadcrumb = json_decode($this->client->getResponse()->getContent());

        $this->assertInternalType('array', $breadcrumb);
        $this->assertEquals(3, count($breadcrumb));
        $this->assertEquals('Ebene 1', $breadcrumb[0]->name);
        $this->assertEquals('Ebene 2', $breadcrumb[1]->name);
        $this->assertEquals('Ebene 3', $breadcrumb[2]->name);
    }

}
