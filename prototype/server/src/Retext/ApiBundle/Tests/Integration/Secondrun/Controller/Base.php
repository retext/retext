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

    protected function getRelationHref($object, $context, $list = false)
    {
        if (!property_exists($object, '@relations')) $this->fail('No @relations in ' . print_r($object, true));
        foreach ($object->{'@relations'} as $relation) {
            if ($relation->relatedcontext == $context && $relation->list === $list) return $relation->href;
        }
        $this->fail('Could not find relation ' . $context . ' (list=' . var_export($list, true) . ' in ' . print_r($object, true));
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
