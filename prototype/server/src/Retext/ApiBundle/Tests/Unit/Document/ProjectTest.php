<?php

namespace Retext\ApiBundle\Tests\Unit\Document;

use Retext\ApiBundle\Document\Project;

class ProjectDocumentTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @group unit
     */
    public function testContributors()
    {
        $project = new Project();
        $this->assertEquals(0, count($project->getContributors()));
        $project->addContributor('hans@wurst.de');
        $project->addContributor('hans@wurst.de');
        $project->addContributor('klaus@wurst.de');
        $project->addContributor('fritz@wurst.de');
        $this->assertEquals(3, count($project->getContributors()));
        $this->assertTrue(in_array('hans@wurst.de', $project->getContributors()));
        $project->removeContributor('klaus@wurst.de');
        $this->assertEquals(2, count($project->getContributors()));
        $project->removeContributor('klaus@wurst.de'); // Should work
        $this->assertEquals(2, count($project->getContributors()));
        $this->assertFalse(in_array('klaus@wurst.de', $project->getContributors()));
        $this->assertTrue(in_array('hans@wurst.de', $project->getContributors()));
        $this->assertTrue(in_array('fritz@wurst.de', $project->getContributors()));
    }
}
