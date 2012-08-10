<?php

namespace Retext\ApiBundle\Tests\Integration\Firstrun\Controller;

use Retext\ApiBundle\Tests\Integration\ApiClient;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Testet die Status-Schnittstelle
 *
 * @author Markus Tacker <m@tckr.cc>
 */
class StatusControllerTest extends WebTestCase
{
    public function testStatus()
    {
        $client = new ApiClient(static::createClient());
        $status = $client->GET('/api/status');
        $this->assertObjectHasAttribute('time', $status);
    }
}
