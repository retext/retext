<?php

namespace Retext\ApiBundle\Document;

use Retext\ApiBundle\Exception\ValidationException;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\ORM\Mapping as Doctrine;
use JMS\SerializerBundle\Annotation as SerializerBundle;

/**
 * @MongoDB\Document
 * @Doctrine\HasLifecycleCallbacks
 */
class Project extends Base
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
     * @MongoDB\ReferenceOne(targetDocument="Retext\ApiBundle\Document\User", cascade={"persist"}, simple=true)
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

    /**
     * @MongoDB\PrePersist
     * @MongoDB\PreUpdate
     */
    public function validate()
    {
        if (empty($this->name)) throw new ValidationException('name', 'empty');
        if (empty($this->owner)) throw new ValidationException('owner', 'empty');
    }

    /**
     * Gibt die Namen der verknüpften Dokumente zurück
     *
     * @return DocumentRelation[]|null
     */
    public function getRelatedDocuments()
    {
        $container = new Container();
        $container->setProject($this);
        return array(
            DocumentRelation::create($container)->setHref($container->getSubject() . '?project=' . $this->getId())->setList(true)
        );
    }
}
