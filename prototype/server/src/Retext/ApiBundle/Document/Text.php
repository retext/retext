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
class Text extends Base implements \Doctrine\ODM\MongoDB\SoftDelete\SoftDeleteable
{
    /**
     * @MongoDB\Id
     * @var string $id
     */
    protected $id;

    /**
     * @MongoDB\ReferenceOne(targetDocument="Retext\ApiBundle\Document\Project", cascade={"persist"}, simple=true)
     * @MongoDB\Index(order="asc")
     * @var \Retext\ApiBundle\Document\Project $project
     * @SerializerBundle\Accessor(getter="getProjectId")
     */
    private $project;

    /**
     * @MongoDB\ReferenceOne(targetDocument="Retext\ApiBundle\Document\TextType", cascade={"persist"}, simple=true)
     * @MongoDB\Index(order="asc")
     * @var \Retext\ApiBundle\Document\TextType $type
     * @SerializerBundle\Accessor(getter="getTypeId")
     */
    private $type;

    /**
     * @MongoDB\String
     * @var string
     */
    protected $name;

    /**
     * @MongoDB\ReferenceOne(targetDocument="Retext\ApiBundle\Document\Container", cascade={"persist"}, simple=true)
     * @MongoDB\Index(order="asc")
     * @var \Retext\ApiBundle\Document\Container $container
     * @SerializerBundle\Accessor(getter="getContainerId")
     */
    private $container;

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
     * Set container
     *
     * @param \Retext\ApiBundle\Document\Container $container
     */
    public function setContainer(\Retext\ApiBundle\Document\Container $container)
    {
        $this->container = $container;
    }

    /**
     * Get container
     *
     * @return \Retext\ApiBundle\Document\Container $container
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * Get container id
     *
     * @return string
     */
    public function getContainerId()
    {
        return $this->container == null ? null : $this->container->getId();
    }

    /**
     * Set project
     *
     * @param \Retext\ApiBundle\Document\Project $project
     */
    public function setProject(\Retext\ApiBundle\Document\Project $project)
    {
        $this->project = $project;
    }

    /**
     * Get project
     *
     * @return \Retext\ApiBundle\Document\Project $project
     */
    public function getProject()
    {
        return $this->project;
    }

    /**
     * Get project id
     *
     * @return string
     */
    public function getProjectId()
    {
        return $this->project->getId();
    }

    /**
     * Set type
     *
     * @param \Retext\ApiBundle\Document\TextType $type
     */
    public function setType(\Retext\ApiBundle\Document\TextType $type)
    {
        $this->type = $type;
    }

    /**
     * Get type
     *
     * @return \Retext\ApiBundle\Document\TextType $type
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Get type id
     *
     * @return string
     */
    public function getTypeId()
    {
        return $this->type->getId();
    }

    /**
     * @MongoDB\PrePersist
     * @MongoDB\PreUpdate
     */
    public function validate()
    {
        if (empty($this->project)) throw new ValidationException('project', 'empty');
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
     * Gibt die Namen der verknüpften Dokumente zurück
     *
     * @return DocumentRelation
     */
    public function getRelatedDocuments()
    {
        return array(
            DocumentRelation::create($this->getProject()),
            DocumentRelation::create($this->getContainer()),
            DocumentRelation::create($this->getType())
        );
    }
}
