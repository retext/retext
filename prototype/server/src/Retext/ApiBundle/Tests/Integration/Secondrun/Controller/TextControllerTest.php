<?php

namespace Retext\ApiBundle\Tests\Integration\Secondrun\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TextControllerTest extends Base
{
    private $project;

    private $root;

    public function setUp()
    {
        parent::setUp();
        $this->client->request('POST', '/api/user', array(), array(), array('HTTP_ACCEPT' => 'application/json', 'HTTP_CONTENT_TYPE' => 'application/json'), json_encode(array('email' => 'phpunit+text@retext.it')));
        $this->client->request('POST', '/api/login', array(), array(), array('HTTP_ACCEPT' => 'application/json', 'HTTP_CONTENT_TYPE' => 'application/json'), json_encode(array('email' => 'phpunit+text@retext.it', 'password' => 'phpunit+text@retext.it')));
        $this->client->request('POST', '/api/project', array(), array(), array('HTTP_ACCEPT' => 'application/json', 'HTTP_CONTENT_TYPE' => 'application/json'), json_encode(array('name' => 'Text-Test-Project')));
        $this->project = json_decode($this->client->getResponse()->getContent());
        $this->client->request('POST', '/api/container', array(), array(), array('HTTP_ACCEPT' => 'application/json', 'HTTP_CONTENT_TYPE' => 'application/json'), json_encode(array('project' => $this->project->id, 'name' => 'root')));
        $this->root = json_decode($this->client->getResponse()->getContent());
    }

    /**
     * @group secondrun
     * @group integration
     */
    public function testCreateText()
    {
        $this->client->request('POST', '/api/text', array(), array(), array('HTTP_ACCEPT' => 'application/json', 'HTTP_CONTENT_TYPE' => 'application/json'), json_encode(array('container' => $this->root->id, 'name' => 'Dies ist eine Überschrift', 'type' => 'Überschrift')));
        $this->assertEquals(201, $this->client->getResponse()->getStatusCode());
        $this->assertNotEmpty($this->client->getResponse()->getHeader('Location'));
        $hl = json_decode($this->client->getResponse()->getContent());
        $this->assertEquals($this->client->getResponse()->getHeader('Location'), $hl->{'@subject'});
        $this->checkText($hl);
        $this->client->request('POST', '/api/text', array(), array(), array('HTTP_ACCEPT' => 'application/json', 'HTTP_CONTENT_TYPE' => 'application/json'), json_encode(array('container' => $this->root->id, 'name' => 'Dies ist eine Unter-Überschrift', 'type' => 'Unter-Überschrift')));
        $sl = json_decode($this->client->getResponse()->getContent());
        $this->checkText($sl);
        $this->client->request('POST', '/api/text', array(), array(), array('HTTP_ACCEPT' => 'application/json', 'HTTP_CONTENT_TYPE' => 'application/json'), json_encode(array('container' => $this->root->id, 'name' => 'Lorem Ipsum', 'type' => 'Fließtext')));
        $text1 = json_decode($this->client->getResponse()->getContent());
        $this->client->request('POST', '/api/text', array(), array(), array('HTTP_ACCEPT' => 'application/json', 'HTTP_CONTENT_TYPE' => 'application/json'), json_encode(array('container' => $this->root->id, 'name' => 'Lorem Ipsum 2', 'type' => 'Fließtext')));
        $text2 = json_decode($this->client->getResponse()->getContent());
        $this->checkText($text1);
        $this->checkText($text2);
    }

    private function checkText(\stdClass $text)
    {
        $this->assertObjectHasAttribute('@context', $text);
        $this->assertEquals('http://jsonld.retext.it/Text', $text->{'@context'});
        $this->assertObjectHasAttribute('@subject', $text);
        $this->assertNotNull($text->{'@subject'});
        $this->assertObjectHasAttribute('project', $text);
        $this->assertNotNull($text->project);
        $this->assertInternalType('string', $text->project);
        $this->assertObjectHasAttribute('container', $text);
        $this->assertNotNull($text->container);
        $this->assertInternalType('string', $text->container);
    }

}
