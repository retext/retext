<?php

namespace Retext\ApiBundle\Tests\Integration\Secondrun\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ProjectControllerTest extends Base
{
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        self::$client->CREATE('/api/user', array('email' => 'phpunit+project@retext.it'));
        self::$client->POST('/api/login', array('email' => 'phpunit+project@retext.it', 'password' => 'phpunit+project@retext.it'));
    }

    /**
     * @group secondrun
     * @group integration
     */
    public function testCreateProject()
    {
        $project = self::$client->CREATE('/api/project', array('name' => 'Test-Project äöß'));
        $this->assertEquals('http://jsonld.retext.it/Project', $project->{'@context'});
        $this->assertObjectHasAttribute('name', $project);
        $this->assertEquals('Test-Project äöß', $project->name);

        $project = self::$client->GET($project->{'@subject'});
        $this->assertObjectHasAttribute('name', $project);
        $this->assertEquals('Test-Project äöß', $project->name);

        $projects = self::$client->GET('/api/project');
        $this->assertInternalType('array', $projects);
        $this->assertEquals(1, count($projects));

        $this->assertObjectHasAttribute('name', $projects[0]);
        $this->assertEquals('Test-Project äöß', $projects[0]->name);
    }

}
