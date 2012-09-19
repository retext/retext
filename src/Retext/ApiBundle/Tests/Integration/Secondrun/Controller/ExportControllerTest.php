<?php

namespace Retext\ApiBundle\Tests\Integration\Secondrun\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Testet die Export-Schnittstelle
 *
 * @author Markus Tacker <m@tckr.cc>
 */
class ExportControllerTest extends Base
{
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        self::$client->CREATE('/user', array('email' => 'phpunit+export@retext.it'));
        self::$client->POST('/login', array('email' => 'phpunit+export@retext.it', 'password' => 'phpunit+export@retext.it'));
    }

    /**
     * @group secondrun
     * @group integration
     */
    public function testExportProject()
    {
        $project = self::$client->CREATE('/project', array('name' => 'Test-Project'));
        $root = self::$client->GET($this->getRelationHref($project, 'http://jsonld.retext.it/Container', false, 'http://jsonld.retext.it/ontology/root'));

        // Create tree
        self::$client->CREATE('/text', array('parent' => $root->id, 'name' => 'Headline'));
        $l1 = self::$client->CREATE('/container', array('parent' => $root->id, 'name' => '1.1'));
        self::$client->CREATE('/text', array('parent' => $root->id, 'name' => 'Copy 1'));
        self::$client->CREATE('/container', array('parent' => $l1->id, 'name' => '1.1.1'));
        self::$client->CREATE('/container', array('parent' => $l1->id, 'name' => '1.1.2'));
        self::$client->CREATE('/text', array('parent' => $l1->id, 'name' => 'Copy 1'));
        self::$client->CREATE('/container', array('parent' => $root->id, 'name' => '1.2'));
        self::$client->CREATE('/container', array('parent' => $root->id, 'name' => '1.3'));

        self::$client->doRequest('GET', '/export/contentbooklet.html?project=' . $project->id, null, null, 'text/html');
        self::$client->doRequest('GET', '/export/contentbooklet.pdf?project=' . $project->id, null, null, 'application/pdf');
    }
}
