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
        $hl = self::$client->CREATE('/api/text', array('parent' => self::$project->rootContainer, 'name' => 'Dies ist eine Überschrift', 'type' => 'Überschrift'));
        $this->checkText($hl);
        $sl = self::$client->CREATE('/api/text', array('parent' => self::$project->rootContainer, 'name' => 'Dies ist eine Unter-Überschrift', 'type' => 'Unter-Überschrift'));
        $this->checkText($sl);
        $text1 = self::$client->CREATE('/api/text', array('parent' => self::$project->rootContainer, 'name' => 'Lorem Ipsum', 'type' => 'Fließtext'));
        $text2 = self::$client->CREATE('/api/text', array('parent' => self::$project->rootContainer, 'name' => 'Lorem Ipsum 2', 'type' => 'Fließtext'));
        $this->checkText($text1);
        $this->checkText($text2);
    }

    /**
     * @group secondrun
     * @group integration
     * @depends testCreateText
     */
    public function testTextTypes()
    {
        $textTypes = self::$client->GET($this->getRelationHref(self::$project, 'http://jsonld.retext.it/TextType', true));
        $this->assertInternalType('array', $textTypes);
        $this->assertEquals(3, count($textTypes));
    }

    /**
     * @group secondrun
     * @group integration
     * @depends testCreateText
     */
    public function testUpdateText()
    {
        $text = self::$client->CREATE('/api/text', array('parent' => self::$project->rootContainer, 'name' => 'Copy 1', 'text' => 'LOREM IPSUM!', 'type' => 'Fließtext'));
        $text = self::$client->UPDATE($text->{'@subject'}, array('text' => 'Lorem Ipsum!'));
        $this->checkText($text);
        $this->assertObjectHasAttribute('text', $text);
        $this->assertEquals('Lorem Ipsum!', $text->text);
        return $text;
    }

    /**
     * @group secondrun
     * @group integration
     * @depends testUpdateText
     */
    public function testTextHasUpdateHistory(\stdClass $text)
    {
        self::$client->UPDATE($text->{'@subject'}, array('text' => 'Lorem Ipsum'));
        $history = self::$client->GET($this->getRelationHref($text, 'http://jsonld.retext.it/TextVersion', true));
        $this->assertInternalType('array', $history);
        $this->assertEquals(3, count($history));
        $this->assertEquals('Lorem Ipsum', $history[0]->text);
        $this->assertEquals('Lorem Ipsum!', $history[1]->text);
        $this->assertEquals('LOREM IPSUM!', $history[2]->text);
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
        $this->assertObjectHasAttribute('parent', $text);
        $this->assertNotNull($text->parent);
        $this->assertInternalType('string', $text->parent);
    }

}
