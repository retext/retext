<?php

namespace Retext\ApiBundle\Tests\Integration\Secondrun\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Testet die Schnittstellen zum Manipulieren von Containern
 *
 * @author Markus Tacker <m@tckr.cc>
 */
class ContainerControllerTest extends Base
{
    private static $project;

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        self::$client->CREATE('/api/user', array('email' => 'phpunit+container@retext.it'));
        self::$client->POST('/api/login', array('email' => 'phpunit+container@retext.it', 'password' => 'phpunit+container@retext.it'));
        self::$project = self::$client->CREATE('/api/project', array('name' => 'Container-Test-Project'));
    }

    /**
     * @group secondrun
     * @group integration
     * @return object
     */
    public function testRootContainerExists()
    {
        // There must be a root container
        $root = self::$client->GET($this->getRelationHref(self::$project, 'http://jsonld.retext.it/Container', false, 'http://jsonld.retext.it/ontology/root'));
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
        self::$client->doRequest('DELETE', $root->{'@subject'}, null, 403);
    }

    /**
     * @group secondrun
     * @group integration
     * @depends testRootContainerExists
     */
    public function testCreateContainer(\stdClass $root)
    {
        self::$client->CREATE('/api/container', array('parent' => $root->id, 'name' => 'Container 1'));
        self::$client->CREATE('/api/container', array('parent' => $root->id, 'name' => 'Container 2'));
        self::$client->CREATE('/api/container', array('parent' => $root->id, 'name' => 'Container 3'));
        self::$client->CREATE('/api/container', array('parent' => $root->id, 'name' => 'Container 4'));
        $container = self::$client->CREATE('/api/container', array('parent' => $root->id, 'name' => 'Container 5'));
        $this->checkContainer($container);

        $container = self::$client->GET($this->getRelationHref($root, 'http://jsonld.retext.it/Container', true, 'http://jsonld.retext.it/ontology/child'));
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
        $project = self::$client->CREATE('/api/project', array('name' => 'No-Name-Container-Test-Project'));
        $container = self::$client->CREATE('/api/container', array('parent' => $project->rootContainer));
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
        $container = self::$client->UPDATE($container->{'@subject'}, array('name' => 'Flummi'));
        $this->checkContainer($container);
        $this->assertEquals('Flummi', $container->name);
    }

    /**
     * @group secondrun
     * @group integration
     * @return object
     */
    public function testContainerReOrder()
    {
        // Create tree
        $project = self::$client->CREATE('/api/project', array('name' => 'ReOrder-Test-Project'));
        $root = self::$client->GET($this->getRelationHref($project, 'http://jsonld.retext.it/Container', false, 'http://jsonld.retext.it/ontology/root'));
        $a = self::$client->CREATE('/api/container', array('parent' => $root->id, 'name' => 'A'));
        $b = self::$client->CREATE('/api/container', array('parent' => $root->id, 'name' => 'B'));
        $c = self::$client->CREATE('/api/container', array('parent' => $root->id, 'name' => 'C'));
        $d = self::$client->CREATE('/api/container', array('parent' => $root->id, 'name' => 'D'));
        $e = self::$client->CREATE('/api/container', array('parent' => $root->id, 'name' => 'E'));

        self::$client->UPDATE($root->{'@subject'}, array('childOrder' => array(
            $a->id, $c->id, $b->id, $d->id, $e->id
        )));

        $containers = self::$client->GET($this->getRelationHref($root, 'http://jsonld.retext.it/Container', true, 'http://jsonld.retext.it/ontology/child'));

        $this->assertEquals('A', $containers[0]->name);
        $this->assertEquals('C', $containers[1]->name);
        $this->assertEquals('B', $containers[2]->name);
        $this->assertEquals('D', $containers[3]->name);
        $this->assertEquals('E', $containers[4]->name);

        return array($root, $containers);
    }


    /**
     * @group secondrun
     * @group integration
     * @depends testContainerReOrder
     * @return object
     */
    public function testContainerDelete($args)
    {
        list ($root, $containers) = $args;
        self::$client->DELETE($containers[2]->{'@subject'});
        self::$client->doRequest('GET', $containers[2]->{'@subject'}, null, 410);
        $containerList = self::$client->GET($this->getRelationHref($root, 'http://jsonld.retext.it/Container', true, 'http://jsonld.retext.it/ontology/child'));
        $this->assertEquals(4, count($containerList), 'There should be only 4 containers now.');
    }

    /**
     * @group secondrun
     * @group integration
     */
    public function testHierarchyContainer()
    {
        // Create project
        $project = self::$client->CREATE('/api/project', array('name' => 'Hierarchy-Container-Test-Project'));
        $root = self::$client->GET($this->getRelationHref($project, 'http://jsonld.retext.it/Container', false, 'http://jsonld.retext.it/ontology/root'));

        // Create tree
        $l1 = self::$client->CREATE('/api/container', array('parent' => $project->rootContainer, 'name' => '1.1'));
        $l2 = self::$client->CREATE('/api/container', array('parent' => $project->rootContainer, 'name' => '1.2'));
        foreach (array($l1, $l2) as $c) {
            $this->checkContainer($c);
            $this->assertObjectHasAttribute('parent', $c);
            $this->assertEquals($c->parent, $root->id);
        }
        $l1_1 = self::$client->CREATE('/api/container', array('parent' => $l1->id, 'name' => '1.1.1'));
        $l1_2 = self::$client->CREATE('/api/container', array('parent' => $l1->id, 'name' => '1.1.2'));
        foreach (array($l1_1, $l1_2) as $c) {
            $this->checkContainer($c);
            $this->assertObjectHasAttribute('parent', $c);
            $this->assertEquals($c->parent, $l1->id);
        }

        // Prüfe root
        $root = self::$client->GET($this->getRelationHref($project, 'http://jsonld.retext.it/Container', false, 'http://jsonld.retext.it/ontology/root'));
        $this->assertInternalType('object', $root);
        $this->assertEquals(2, $root->childCount);

        // Prüfe Level 1
        $rootChilds = self::$client->GET($this->getRelationHref($root, 'http://jsonld.retext.it/Container', true, 'http://jsonld.retext.it/ontology/child'));
        $this->assertInternalType('array', $rootChilds);
        $this->assertEquals(2, count($rootChilds));
        $this->assertEquals('1.1', $rootChilds[0]->name);
        $this->assertEquals(2, $rootChilds[0]->childCount);
        $this->assertEquals('1.2', $rootChilds[1]->name);
        $this->assertEquals(0, $rootChilds[1]->childCount);

        // Prüfe Level 2
        $l1childs = self::$client->GET($this->getRelationHref($rootChilds[0], 'http://jsonld.retext.it/Container', true, 'http://jsonld.retext.it/ontology/child'));
        $this->assertInternalType('array', $l1childs);
        $this->assertEquals(2, count($l1childs));
        $this->assertEquals('1.1.1', $l1childs[0]->name);
        $this->assertEquals('1.1.2', $l1childs[1]->name);
        $this->assertEquals(0, $l1childs[0]->childCount);
        $this->assertEquals(0, $l1childs[1]->childCount);

        $l2childs = self::$client->GET($this->getRelationHref($rootChilds[1], 'http://jsonld.retext.it/Container', true, 'http://jsonld.retext.it/ontology/child'));
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
        $project = self::$client->CREATE('/api/project', array('name' => 'Breadcrumb-Test-Project'));
        $root = self::$client->GET($this->getRelationHref($project, 'http://jsonld.retext.it/Container', false, 'http://jsonld.retext.it/ontology/root'));
        // Create tree
        $l1 = self::$client->CREATE('/api/container', array('parent' => $root->id, 'name' => 'Ebene 1'));
        $l2 = self::$client->CREATE('/api/container', array('parent' => $l1->id, 'name' => 'Ebene 2'));
        $l3 = self::$client->CREATE('/api/container', array('parent' => $l2->id, 'name' => 'Ebene 3'));
        $breadcrumb = self::$client->GET($this->getRelationHref($l3, 'http://jsonld.retext.it/Breadcrumb', true));

        $this->assertInternalType('array', $breadcrumb);
        $this->assertEquals(3, count($breadcrumb));
        $this->assertEquals('Ebene 1', $breadcrumb[0]->name);
        $this->assertEquals('Ebene 2', $breadcrumb[1]->name);
        $this->assertEquals('Ebene 3', $breadcrumb[2]->name);
    }

    /**
     * @group secondrun
     * @group integration
     */
    public function testOrderWithMixedElements()
    {
        $project = self::$client->CREATE('/api/project', array('name' => 'Mixed-Order-Test-Project'));
        $root = self::$client->GET($this->getRelationHref($project, 'http://jsonld.retext.it/Container', false, 'http://jsonld.retext.it/ontology/root'));
        $header = self::$client->CREATE('/api/container', array('parent' => $root->id, 'name' => 'Header'));
        $headline = self::$client->CREATE('/api/text', array('parent' => $root->id, 'name' => 'Headline'));
        $footer = self::$client->CREATE('/api/container', array('parent' => $root->id, 'name' => 'Footer'));
        $rootChilds = self::$client->GET($this->getRelationHref($root, 'http://jsonld.retext.it/Element', true, 'http://jsonld.retext.it/ontology/child'));
        $this->assertInternalType('array', $rootChilds);
        $this->assertEquals(3, count($rootChilds));
        $this->assertEquals('Header', $rootChilds[0]->name);
        $this->assertEquals('Headline', $rootChilds[1]->name);
        $this->assertEquals('Footer', $rootChilds[2]->name);

        self::$client->UPDATE($root->{'@subject'}, array('childOrder' => array(
            $header->id, $footer->id, $headline->id
        )));
        $rootChilds = self::$client->GET($this->getRelationHref($root, 'http://jsonld.retext.it/Element', true, 'http://jsonld.retext.it/ontology/child'));
        $this->assertEquals('Header', $rootChilds[0]->name);
        $this->assertEquals('Footer', $rootChilds[1]->name);
        $this->assertEquals('Headline', $rootChilds[2]->name);
    }

    /**
     * @group secondrun
     * @group integration
     */
    public function testTreeWithMixedElements()
    {
        // Create project
        $project = self::$client->CREATE('/api/project', array('name' => 'Tree-Test-Project'));
        $root = self::$client->GET($this->getRelationHref($project, 'http://jsonld.retext.it/Container', false, 'http://jsonld.retext.it/ontology/root'));

        // Create tree
        self::$client->CREATE('/api/text', array('parent' => $root->id, 'name' => 'Headline'));
        $l1 = self::$client->CREATE('/api/container', array('parent' => $root->id, 'name' => '1.1'));
        self::$client->CREATE('/api/text', array('parent' => $root->id, 'name' => 'Copy 1'));
        self::$client->CREATE('/api/container', array('parent' => $l1->id, 'name' => '1.1.1'));
        self::$client->CREATE('/api/container', array('parent' => $l1->id, 'name' => '1.1.2'));
        self::$client->CREATE('/api/text', array('parent' => $l1->id, 'name' => 'Copy 1'));
        self::$client->CREATE('/api/container', array('parent' => $root->id, 'name' => '1.2'));
        self::$client->CREATE('/api/container', array('parent' => $root->id, 'name' => '1.3'));

        $tree = self::$client->GET($this->getRelationHref($project, 'http://jsonld.retext.it/Element', true, 'http://jsonld.retext.it/ontology/tree'));
        $this->assertInternalType('array', $tree);
        $this->assertEquals(5, count($tree));
        $this->assertEquals($tree[0]->data->name, 'Headline');
        $this->assertEquals($tree[0]->data->{'@context'}, 'http://jsonld.retext.it/Text');
        $this->assertEquals(0, count($tree[0]->children));
        $this->assertEquals($tree[1]->data->name, '1.1');
        $this->assertEquals($tree[1]->data->{'@context'}, 'http://jsonld.retext.it/Container');
        $this->assertEquals(3, count($tree[1]->children));
        $this->assertEquals($tree[2]->data->name, 'Copy 1');
        $this->assertEquals($tree[2]->data->{'@context'}, 'http://jsonld.retext.it/Text');
        $this->assertEquals(0, count($tree[2]->children));
        $this->assertEquals($tree[3]->data->name, '1.2');
        $this->assertEquals($tree[3]->data->{'@context'}, 'http://jsonld.retext.it/Container');
        $this->assertEquals(0, count($tree[3]->children));
        $this->assertEquals($tree[4]->data->name, '1.3');
        $this->assertEquals($tree[4]->data->{'@context'}, 'http://jsonld.retext.it/Container');
        $this->assertEquals(0, count($tree[4]->children));

        $this->assertEquals($tree[1]->children[0]->data->name, '1.1.1');
        $this->assertEquals($tree[1]->children[0]->data->{'@context'}, 'http://jsonld.retext.it/Container');
        $this->assertEquals($tree[1]->children[1]->data->name, '1.1.2');
        $this->assertEquals($tree[1]->children[1]->data->{'@context'}, 'http://jsonld.retext.it/Container');
        $this->assertEquals($tree[1]->children[2]->data->name, 'Copy 1');
        $this->assertEquals($tree[1]->children[2]->data->{'@context'}, 'http://jsonld.retext.it/Text');
    }

}
