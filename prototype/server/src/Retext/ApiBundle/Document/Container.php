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
class Container extends Base implements \Doctrine\ODM\MongoDB\SoftDelete\SoftDeleteable
{
    /**
     * @MongoDB\Id
     * @var string $id
     */
    private $id;

    /**
     * @MongoDB\ReferenceOne(targetDocument="Retext\ApiBundle\Document\Project", cascade={"persist"}, simple=true)
     * @MongoDB\Index(order="asc")
     * @var \Retext\ApiBundle\Document\Project $project
     * @SerializerBundle\Accessor(getter="getProjectId")
     */
    private $project;

    /**
     * @MongoDB\String
     * @var string
     */
    private $name;

    /**
     * @MongoDB\ReferenceOne(targetDocument="Retext\ApiBundle\Document\Container", cascade={"persist"}, simple=true)
     * @MongoDB\Index(order="asc")
     * @var \Retext\ApiBundle\Document\Container $parent
     * @SerializerBundle\Accessor(getter="getParentId")
     */
    private $parent;

    /**
     * @MongoDB\Boolean
     * @var bool
     * @SerializerBundle\SerializedName("isRootContainer")
     */
    private $rootContainer = false;

    /**
     * @MongoDB\Int
     * @var int
     * @SerializerBundle\SerializedName("childCount")
     */
    private $childCount = 0;

    /**
     * @MongoDB\Hash
     * @var array
     * @SerializerBundle\Exclude
     */
    private $childOrder = array();

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
     * Set parent
     *
     * @param \Retext\ApiBundle\Document\Container $parent
     */
    public function setParent(\Retext\ApiBundle\Document\Container $parent)
    {
        $this->parent = $parent;
    }

    /**
     * Get parent
     *
     * @return \Retext\ApiBundle\Document\Container $parent
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Get parent id
     *
     * @return string
     */
    public function getParentId()
    {
        return $this->parent == null ? null : $this->parent->getId();
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
     * Set rootContainer
     *
     * @param boolean $rootContainer
     */
    public function setRootContainer($rootContainer)
    {
        $this->rootContainer = $rootContainer;
    }

    /**
     * Get rootContainer
     *
     * @return boolean $rootContainer
     */
    public function getRootContainer()
    {
        return $this->rootContainer;
    }

    /**
     * @return boolean $rootContainer
     */
    public function isRootContainer()
    {
        return $this->getRootContainer();
    }

    /**
     * Gibt die Namen der verknüpften Dokumente zurück
     *
     * @return DocumentRelation
     */
    public function getRelatedDocuments()
    {
        $container = new Container();
        $text = new Text();
        $breadcrumb = new Breadcrumb();
        return array(
            DocumentRelation::create($this->getProject()),
            DocumentRelation::create($container)->setHref($container->getSubject() . '?parent=' . $this->getId())->setList(true)->setRole('http://jsonld.retext.it/ontology/child'),
            DocumentRelation::create($text)->setHref($text->getSubject() . '?parent=' . $this->getId())->setList(true),
            DocumentRelation::create($breadcrumb)->setHref($this->getSubject() . '/breadcrumb')->setList(true),
        );
    }

    /**
     * Set childCount
     *
     * @param int $childCount
     */
    public function setChildCount($childCount)
    {
        $this->childCount = $childCount;
    }

    /**
     * Get childCount
     *
     * @return int $childCount
     */
    public function getChildCount()
    {
        return $this->childCount;
    }

    /**
     * Set childOrder
     *
     * @param array $childOrder
     */
    public function setChildOrder(array $childOrder)
    {
        $this->childOrder = $childOrder;
    }

    /**
     * Get childOrder
     *
     * @return array $childOrder
     */
    public function getChildOrder()
    {
        return $this->childOrder;
    }
}
