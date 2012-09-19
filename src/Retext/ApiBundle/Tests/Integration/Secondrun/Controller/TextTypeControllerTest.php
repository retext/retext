<?php

namespace Retext\ApiBundle\Tests\Integration\Secondrun\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Testet die Schnittstellen zum manipulieren von Text-Typen
 *
 * @author Markus Tacker <m@tckr.cc>
 */
class TextTypeControllerTest extends Base
{
    private static $project;

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        self::$client->CREATE('/user', array('email' => 'phpunit+texttype@retext.it'));
        self::$client->POST('/login', array('email' => 'phpunit+texttype@retext.it', 'password' => 'phpunit+texttype@retext.it'));
        self::$project = self::$client->CREATE('/project', array('name' => 'TextType-Test-Project'));
    }

    /**
     * @group secondrun
     * @group integration
     */
    public function testGetType()
    {
        $hl = self::$client->CREATE('/text', array('parent' => self::$project->rootContainer, 'name' => 'Dies ist eine Überschrift', 'type' => 'Überschrift'));
        $type = self::$client->GET($this->getRelationHref($hl, 'http://jsonld.retext.it/TextType', false));
        $this->assertObjectHasAttribute('@context', $type);
        $this->assertEquals('http://jsonld.retext.it/TextType', $type->{'@context'});
        $this->assertObjectHasAttribute('@subject', $type);
        $this->assertNotNull($type->{'@subject'});
        $this->assertObjectHasAttribute('project', $type);
        $this->assertNotNull($type->project);
        $this->assertInternalType('string', $type->project);
        return $type;
    }

    /**
     * @group secondrun
     * @group integration
     * @depends testGetType
     * @param \stdClass $type
     */
    public function testUpdateType(\stdClass $type)
    {
        self::$client->UPDATE($type->{'@subject'}, array('name' => 'Headline', 'fontsize' => 14, 'fontname' => 'Comic Sans'));
        $hl = self::$client->GET($type->{'@subject'});
        $this->assertEquals('Headline', $hl->name);
        $this->assertEquals(14, $hl->fontsize);
        $this->assertEquals('Comic Sans', $hl->fontname);
    }
}
