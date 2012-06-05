<?php

namespace Retext\ApiBundle\Tests\Integration\Secondrun\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TextControllerTest extends Base
{
    private static $project;

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        self::$client->CREATE('/api/user', array('email' => 'phpunit+text@retext.it'));
        self::$client->POST('/api/login', array('email' => 'phpunit+text@retext.it', 'password' => 'phpunit+text@retext.it'));
        self::$project = self::$client->CREATE('/api/project', array('name' => 'Text-Test-Project'));
    }

    /**
     * @group secondrun
     * @group integration
     */
    public function testCreateText()
    {
        $hl = self::$client->CREATE('/api/text', array('container' => self::$project->rootContainer, 'name' => 'Dies ist eine Überschrift', 'type' => 'Überschrift'));
        $this->checkText($hl);
        $sl = self::$client->CREATE('/api/text', array('container' => self::$project->rootContainer, 'name' => 'Dies ist eine Unter-Überschrift', 'type' => 'Unter-Überschrift'));
        $this->checkText($sl);
        $text1 = self::$client->CREATE('/api/text', array('container' => self::$project->rootContainer, 'name' => 'Lorem Ipsum', 'type' => 'Fließtext'));
        $text2 = self::$client->CREATE('/api/text', array('container' => self::$project->rootContainer, 'name' => 'Lorem Ipsum 2', 'type' => 'Fließtext'));
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
