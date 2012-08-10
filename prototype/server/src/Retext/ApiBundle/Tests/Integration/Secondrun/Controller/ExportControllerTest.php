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
        self::$client->CREATE('/api/user', array('email' => 'phpunit+export@retext.it'));
        self::$client->POST('/api/login', array('email' => 'phpunit+export@retext.it', 'password' => 'phpunit+export@retext.it'));
    }

    /**
     * @group secondrun
     * @group integration
     */
    public function testExportProject()
    {
        $project = self::$client->CREATE('/api/project', array('name' => 'Test-Project'));
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

        self::$client->doRequest('GET', '/api/export/contentbooklet.html?project=' . $project->id, null, null, 'text/html');
        self::$client->doRequest('GET', '/api/export/contentbooklet.pdf?project=' . $project->id, null, null, 'application/pdf');
    }
}
