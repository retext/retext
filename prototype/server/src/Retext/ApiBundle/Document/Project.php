<?php

namespace Retext\ApiBundle\Document;

use Retext\ApiBundle\Exception\ValidationException, Retext\ApiBundle\Document\TextType;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\ORM\Mapping as Doctrine;
use JMS\SerializerBundle\Annotation as SerializerBundle;

/**
 * @MongoDB\Document
 * @Doctrine\HasLifecycleCallbacks
 */
class Project extends Base implements \Doctrine\ODM\MongoDB\SoftDelete\SoftDeleteable
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
     * @MongoDB\ReferenceOne(targetDocument="Retext\ApiBundle\Document\Container", cascade={"persist"}, simple=true)
     * @MongoDB\Index(order="asc")
     * @SerializerBundle\SerializedName("rootContainer")
     * @SerializerBundle\Accessor(getter="getRootContainerId")
     * @var \Retext\ApiBundle\Document\Container
     */
    private $rootContainer;

    /**
     * @MongoDB\Date
     * @MongoDB\Index(order="asc")
     * @var \DateTime|null
     */
    private $deletedAt = null;

    /**
     * Get id
     *
     * @return string $id
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
     * Gets the date that this object was deleted at.
     *
     * @return \DateTime $deletedAt
     */
    public function getDeletedAt()
    {
        return $this->deletedAt;
    }

    /**
     * Set deletedAt
     *
     * @param \DateTime $deletedAt
     */
    public function setDeletedAt(\DateTime $deletedAt)
    {
        $this->deletedAt = $deletedAt;
    }

    /**
     * Set rootContainer
     *
     * @param \Retext\ApiBundle\Document\Container $rootContainer
     */
    public function setRootContainer(\Retext\ApiBundle\Document\Container $rootContainer)
    {
        $this->rootContainer = $rootContainer;
    }

    /**
     * Get rootContainer
     *
     * @return \Retext\ApiBundle\Document\Container $rootContainer
     */
    public function getRootContainer()
    {
        return $this->rootContainer;
    }

    /**
     * Get rootContainer id
     *
     * @return string
     */
    public function getRootContainerId()
    {
        return $this->rootContainer->getId();
    }

    /**
     * Gibt die Namen der verknüpften Dokumente zurück
     *
     * @return DocumentRelation[]|null
     */
    public function getRelatedDocuments()
    {
        $rootContainer = $this->getRootContainer();
        $textType = new TextType();
        return array(
            DocumentRelation::createFromDoc($rootContainer)->setHref($rootContainer->getSubject())->setRole('http://jsonld.retext.it/ontology/root'),
            DocumentRelation::createFromDoc($textType)->setHref($textType->getSubject() . '?project=' . $this->getId())->setList(true),
        );
    }
}
