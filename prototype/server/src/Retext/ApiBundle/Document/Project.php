<?php

namespace Retext\ApiBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use JMS\SerializerBundle\Annotation as SerializerBundle;

/**
 * @MongoDB\Document
 */
class Project
{
    /**
     * @MongoDB\Id
     * @var $id
     */
    protected $id;

    /**
     * @MongoDB\String
     */
    protected $name;

    /**
     * @MongoDB\ReferenceOne(targetDocument="Retext\ApiBundle\Document\User", cascade={"persist"})
     * @MongoDB\Index(order="asc")
     */
    private $owner;

    /**
     * Get id
     *
     * @return id $id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Get name
     *
     * @return string $name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set owner
     *
     * @param \Retext\ApiBundle\Document\User $owner
     */
    public function setOwner(\Retext\ApiBundle\Document\User $owner)
    {
        $this->owner = $owner;
    }

    /**
     * Get owner
     *
     * @return \Retext\ApiBundle\Document\User $owner
     */
    public function getOwner()
    {
        return $this->owner;
    }
}
