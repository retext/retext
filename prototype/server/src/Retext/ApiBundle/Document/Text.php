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
class Text extends Base implements \Doctrine\ODM\MongoDB\SoftDelete\SoftDeleteable, Element
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
     * @SerializerBundle\Accessor(getter="getTypeName")
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
     * @var \Retext\ApiBundle\Document\Container $parent
     * @SerializerBundle\Accessor(getter="getParentId")
     */
    private $parent;

    /**
     * @MongoDB\String
     */
    private $text;

    /**
     * @MongoDB\Int
     * @var int
     * @SerializerBundle\SerializedName("commentCount")
     */
    private $commentCount = 0;

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
     * Get type id
     *
     * @return string
     */
    public function getTypeName()
    {
        return $this->type->getName();
    }

    /**
     * Set text
     *
     * @param string $text
     */
    public function setText($text)
    {
        $this->text = $text;
    }

    /**
     * Get text
     *
     * @return string $text
     */
    public function getText()
    {
        return $this->text;
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
        $types = new TextType();
        $versions = new TextVersion();
        $comments = new Comment();
        return array(
            DocumentRelation::createFromDoc($this->getProject()),
            DocumentRelation::createFromDoc($this->getParent()),
            DocumentRelation::createFromDoc($this->getType()),
            DocumentRelation::createFromDoc($types)->setHref($types->getSubject() . '?project=' . $this->getProjectId())->setList(true),
            DocumentRelation::createFromDoc($versions)->setHref($this->getSubject() . '/history')->setList(true),
            DocumentRelation::createFromDoc($comments)->setHref($this->getSubject() . '/comments')->setList(true),
        );
    }

    /**
     * Set commentCount
     *
     * @param int $commentCount
     */
    public function setCommentCount($commentCount)
    {
        $this->commentCount = $commentCount;
    }

    /**
     * Get commentCount
     *
     * @return int $commentCount
     */
    public function getCommentCount()
    {
        return $this->commentCount;
    }
}
