<?php

namespace Retext\ApiBundle\Tests\Integration\Secondrun\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ProjectControllerTest extends WebTestCase
{
    /**
     * @var \Symfony\Bundle\FrameworkBundle\Client
     */
    private $client;

    public function setUp()
    {
        $this->client = static::createClient();
        $this->client->request('POST', '/api/login', array('email' => 'phpunit@retext.it', 'password' => 'phpunit@retext.it'));
    }

    /**
     * @depend Retext\ApiBundle\Tests\Integration\Controller\RegisterController::testRegister
     * @group secondrun
     * @group integration
     */
    public function testCreateProject()
    {
        $this->client->request('POST', '/api/project', array('name' => 'Test-Project äöß'));
        $this->assertEquals(201, $this->client->getResponse()->getStatusCode());
        $this->assertNotEmpty($this->client->getResponse()->headers->get('Location'));
        $project = json_decode($this->client->getResponse()->getContent());
        $this->assertObjectHasAttribute('name', $project);
        $this->assertEquals('Test-Project äöß', $project->name);

        $this->client->request('GET', $this->client->getResponse()->headers->get('Location'));
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $project = json_decode($this->client->getResponse()->getContent());
        $this->assertObjectHasAttribute('name', $project);
        $this->assertEquals('Test-Project äöß', $project->name);

        $this->client->request('GET', '/api/project');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $projects = json_decode($this->client->getResponse()->getContent());
        $this->assertEquals(1, count($projects));
        $this->assertObjectHasAttribute('name', $projects[0]);
        $this->assertEquals('Test-Project äöß', $projects[0]->name);
    }

}
