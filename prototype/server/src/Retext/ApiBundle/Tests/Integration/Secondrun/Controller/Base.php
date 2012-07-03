<?php

namespace Retext\ApiBundle\Tests\Integration\Secondrun\Controller;

use Retext\ApiBundle\Tests\Integration\ApiClient;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Basisklasse für Integrationstests
 *
 * @author Markus Tacker <m@tckr.cc>
 */
abstract class Base extends WebTestCase
{
    /**
     * @var \Retext\ApiBundle\Tests\Integration\ApiClient
     */
    protected static $client;

    public static function setUpBeforeClass()
    {
        self::$client = new ApiClient(static::createClient());
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
        return self::$client->GET($this->getRelationHref($object, $context));
    }
}
