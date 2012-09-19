<?php

namespace Retext\ApiBundle\Document;

use Retext\ApiBundle\Exception\ValidationException;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\ORM\Mapping as Doctrine;
use JMS\SerializerBundle\Annotation as SerializerBundle;

/**
 * Ein Container. Kann weitere Container und Texten enthalten.
 *
 * @author Markus Tacker <m@tckr.cc>
 *
 * @MongoDB\Document
 * @Doctrine\HasLifecycleCallbacks
 */
class Container extends \Retext\ApiBundle\Model\Base implements \Doctrine\ODM\MongoDB\SoftDelete\SoftDeleteable, \Retext\ApiBundle\Model\Element
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
     * @var \Retext\ApiBundle\Model\Element[] $element
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
     * @return string $id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param \Retext\ApiBundle\Document\Container $parent
     */
    public function setParent(\Retext\ApiBundle\Document\Container $parent)
    {
        $this->parent = $parent;
    }

    /**
     * @return \Retext\ApiBundle\Document\Container $parent
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @return string
     */
    public function getParentId()
    {
        return $this->parent == null ? null : $this->parent->getId();
    }

    /**
     * @param \Retext\ApiBundle\Document\Project $project
     */
    public function setProject(\Retext\ApiBundle\Document\Project $project)
    {
        $this->project = $project;
    }

    /**
     * @return \Retext\ApiBundle\Document\Project $project
     */
    public function getProject()
    {
        return $this->project;
    }

    /**
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
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
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
     * @param \DateTime $deletedAt
     */
    public function setDeletedAt(\DateTime $deletedAt)
    {
        $this->deletedAt = $deletedAt;
    }

    /**
     * @param boolean $rootContainer
     */
    public function setRootContainer($rootContainer)
    {
        $this->rootContainer = $rootContainer;
    }

    /**
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
     * @return \Retext\ApiBundle\Model\DocumentRelation
     */
    public function getRelatedDocuments()
    {
        $container = new Container();
        $text = new Text();
        $breadcrumb = new \Retext\ApiBundle\Model\Breadcrumb();
        return array(
            \Retext\ApiBundle\Model\DocumentRelation::createFromDoc($this->getProject()),
            \Retext\ApiBundle\Model\DocumentRelation::createFromDoc($container)->setHref($container->getSubject() . '?parent=' . $this->getId())->setList(true)->setRole('http://jsonld.retext.it/ontology/child'),
            \Retext\ApiBundle\Model\DocumentRelation::createFromDoc($text)->setHref($text->getSubject() . '?parent=' . $this->getId())->setList(true),
            \Retext\ApiBundle\Model\DocumentRelation::createFromDoc($breadcrumb)->setHref($this->getSubject() . '/breadcrumb')->setList(true),
            \Retext\ApiBundle\Model\DocumentRelation::create()->setRelatedcontext('http://jsonld.retext.it/Element')->setList(true)->setRole('http://jsonld.retext.it/ontology/child')->setHref('/element?parent=' . $this->getId()),
            \Retext\ApiBundle\Model\DocumentRelation::create()->setRelatedcontext('http://jsonld.retext.it/Element')->setList(true)->setRole('http://jsonld.retext.it/ontology/tree')->setHref($this->getSubject() . '/tree'),
        );
    }

    /**
     * @param int $childCount
     */
    public function setChildCount($childCount)
    {
        $this->childCount = $childCount;
    }

    /**
     * @return int $childCount
     */
    public function getChildCount()
    {
        return $this->childCount;
    }

    /**
     * @param array $childOrder
     */
    public function setChildOrder(array $childOrder)
    {
        $this->childOrder = $childOrder;
    }

    /**
     * @return array $childOrder
     */
    public function getChildOrder()
    {
        return $this->childOrder;
    }
}
