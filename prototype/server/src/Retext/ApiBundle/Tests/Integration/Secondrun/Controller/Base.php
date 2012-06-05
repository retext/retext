<?php

namespace Retext\ApiBundle\Tests\Integration\Secondrun\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class Base extends WebTestCase
{
    /**
     * @var \Symfony\Bundle\FrameworkBundle\Client
     */
    protected $client;

    protected function setUp()
    {
        $this->client = static::createClient();
    }

    protected function getRelationHref($object, $context, $list = false, $role = null)
    {
        if ($object === null || !property_exists($object, '@relations')) $this->fail('No @relations in ' . var_export($object, true));
        foreach ($object->{'@relations'} as $relation) {
            if ($relation->relatedcontext != $context) continue;
            if ($relation->list !== $list) continue;
            if ($role !== null && $role !== $relation->role) continue;
            return $relation->href;
        }
        $this->fail('Could not find relation ' . $context . ' (list=' . var_export($list, true) . ', role=' . var_export($role, true) . ') in ' . var_export($object, true));
    }

    /**
     * @param object $object
     * @param string $context
     * @return object
     */
    protected function fetchRelation($object, $context)
    {
        $this->client->request('GET', $this->getRelationHref($object, $context), array(), array(), array('HTTP_ACCEPT' => 'application/json'));
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        return json_decode($this->client->getResponse()->getContent());
    }
}
