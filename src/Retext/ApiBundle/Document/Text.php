<?php

namespace Retext\ApiBundle\Document;

use Retext\ApiBundle\Exception\ValidationException;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\ORM\Mapping as Doctrine;
use JMS\SerializerBundle\Annotation as SerializerBundle;

/**
 * Repräsentiert die Texte.
 *
 * @author Markus Tacker <m@tckr.cc>
 *
 * @MongoDB\Document
 * @Doctrine\HasLifecycleCallbacks
 * @MongoDB\UniqueIndex(keys={"project"="asc", "identifier"="asc"})
 */
class Text extends \Retext\ApiBundle\Model\Base implements \Doctrine\ODM\MongoDB\SoftDelete\SoftDeleteable, \Retext\ApiBundle\Model\Element
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
     * @var array
     * @SerializerBundle\Accessor(getter="getTypeData")
     * @SerializerBundle\SerializedName("typeData")
     */
    private $typeData;

    /**
     * @MongoDB\String
     * @var string
     */
    protected $name;

    /**
     * @MongoDB\String
     * @var string
     */
    protected $description;

    /**
     * @MongoDB\String
     * @var string
     */
    protected $identifier;

    /**
     * @MongoDB\ReferenceOne(targetDocument="Retext\ApiBundle\Document\Container", cascade={"persist"}, simple=true)
     * @MongoDB\Index(order="asc")
     * @var \Retext\ApiBundle\Document\Container $parent
     * @SerializerBundle\Accessor(getter="getParentId")
     */
    private $parent;

    /**
     * @MongoDB\Hash
     * @var array
     */
    private $text = null;

    /**
     * @MongoDB\Int
     * @var int
     * @SerializerBundle\SerializedName("commentCount")
     */
    private $commentCount = 0;

    /**
     * Rechtschreibung in Ordnung
     *
     * @MongoDB\Boolean
     * @SerializerBundle\SerializedName("spellingApproved")
     * @var bool
     */
    private $spellingApproved = false;

    /**
     * Inhalt in Ordnung
     *
     * @MongoDB\Boolean
     * @SerializerBundle\SerializedName("contentApproved")
     * @var bool
     */
    private $contentApproved = false;

    /**
     * Freigabe erteilt
     *
     * @MongoDB\Boolean
     * @var bool
     */
    private $approved = false;

    /**
     * Fortschritt der Freigabe
     *
     * @var float
     * @SerializerBundle\Accessor(getter="getApprovedProgress")
     * @SerializerBundle\SerializedName("approvedProgress")
     */
    private $approvedProgress;

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
     * @param \Retext\ApiBundle\Document\TextType $type
     */
    public function setType(\Retext\ApiBundle\Document\TextType $type)
    {
        $this->type = $type;
    }

    /**
     * @return \Retext\ApiBundle\Document\TextType $type
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getTypeId()
    {
        return $this->type->getId();
    }

    /**
     * @return string
     */
    public function getTypeName()
    {
        return $this->type->getName();
    }

    /**
     * @return array
     */
    public function getTypeData()
    {
        return array(
            'name' => $this->type->getName(),
            'fontname' => $this->type->getFontname(),
            'fontsize' => $this->type->getFontsize(),
            'multiline' => $this->type->getMultiline(),
            'description' => $this->type->getDescription(),
        );
    }

    /**
     * @param array $text
     */
    public function setText(array $text = null)
    {
        $this->text = $text;
    }

    /**
     * Set text for a language
     *
     * @param string $language
     * @param array $text
     */
    public function setLanguageText($language, $text)
    {
        $this->text[$language] = $text;
    }

    /**
     * @return array $text
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
        if (empty($this->identifier)) throw new ValidationException('identifier', 'empty');
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
     * Gibt die Namen der verknüpften Dokumente zurück
     *
     * @return \Retext\ApiBundle\Model\DocumentRelation
     */
    public function getRelatedDocuments()
    {
        $types = new TextType();
        $versions = new TextVersion();
        $comments = new Comment();
        return array(
            \Retext\ApiBundle\Model\DocumentRelation::createFromDoc($this->getProject()),
            \Retext\ApiBundle\Model\DocumentRelation::createFromDoc($this->getParent()),
            \Retext\ApiBundle\Model\DocumentRelation::createFromDoc($this->getType()),
            \Retext\ApiBundle\Model\DocumentRelation::createFromDoc($types)->setHref($types->getSubject() . '?project=' . $this->getProjectId())->setList(true),
            \Retext\ApiBundle\Model\DocumentRelation::createFromDoc($versions)->setHref($this->getSubject() . '/history')->setList(true),
            \Retext\ApiBundle\Model\DocumentRelation::createFromDoc($comments)->setHref($this->getSubject() . '/comments')->setList(true),
        );
    }

    /**
     * @param int $commentCount
     */
    public function setCommentCount($commentCount)
    {
        $this->commentCount = $commentCount;
    }

    /**
     * @return int $commentCount
     */
    public function getCommentCount()
    {
        return $this->commentCount;
    }

    /**
     * @param boolean $spellingApproved
     */
    public function setSpellingApproved($spellingApproved)
    {
        $this->spellingApproved = $spellingApproved;
    }

    /**
     * @return boolean $spellingApproved
     */
    public function getSpellingApproved()
    {
        return $this->spellingApproved;
    }

    /**
     * @param boolean $contentApproved
     */
    public function setContentApproved($contentApproved)
    {
        $this->contentApproved = $contentApproved;
    }

    /**
     * @return boolean $contentApproved
     */
    public function getContentApproved()
    {
        return $this->contentApproved;
    }

    /**
     * @param boolean $approved
     */
    public function setApproved($approved)
    {
        $this->approved = $approved;
    }

    /**
     * @return boolean $approved
     */
    public function getApproved()
    {
        return $this->approved;
    }

    /**
     * @return float
     */
    public function getApprovedProgress()
    {
        $approvedCount = 0;
        if ($this->spellingApproved) $approvedCount++;
        if ($this->contentApproved) $approvedCount++;
        if ($this->approved) $approvedCount++;
        $this->approvedProgress = $approvedCount / 3;
        return $this->approvedProgress;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $identifier
     */
    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;
    }

    /**
     * @return string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }
}
