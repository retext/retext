<?php

namespace Retext\ApiBundle\Document;

use Retext\ApiBundle\Exception\ValidationException;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\ORM\Mapping as Doctrine;
use JMS\SerializerBundle\Annotation as SerializerBundle;

/**
 * Repräsentiert eine Version des Inhaltes eines Textes
 *
 * @author Markus Tacker <m@tckr.cc>
 *
 * @MongoDB\Document
 * @Doctrine\HasLifecycleCallbacks
 */
class TextVersion extends \Retext\ApiBundle\Model\Base implements \Doctrine\ODM\MongoDB\SoftDelete\SoftDeleteable
{
    /**
     * @MongoDB\Id
     * @MongoDB\Index(order="desc")
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
     * @MongoDB\ReferenceOne(targetDocument="Retext\ApiBundle\Document\Text", cascade={"persist"}, simple=true)
     * @MongoDB\Index(order="asc")
     * @var \Retext\ApiBundle\Document\Text $parent
     * @SerializerBundle\Accessor(getter="getParentId")
     */
    private $parent;

    /**
     * @MongoDB\Hash
     * @var array $text
     */
    private $text;

    /**
     * @MongoDB\Date
     * @MongoDB\Index(order="desc")
     * @SerializerBundle\SerializedName("createdAt")
     * @var \DateTime
     */
    private $createdAt;

    /**
     * @MongoDB\Date
     * @MongoDB\Index(order="asc")
     * @var \DateTime|null
     */
    private $deletedAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    /**
     * @return string $id
     */
    public function getId()
    {
        return $this->id;
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
     * @param \Retext\ApiBundle\Document\Text $parent
     */
    public function setParent(\Retext\ApiBundle\Document\Text $parent)
    {
        $this->parent = $parent;
    }

    /**
     * @return \Retext\ApiBundle\Document\Text $parent
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
        return array(
            \Retext\ApiBundle\Model\DocumentRelation::createFromDoc($this->getProject()),
            \Retext\ApiBundle\Model\DocumentRelation::createFromDoc($this->getParent()),
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
     * Set text for a langueg
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
     * @param \DateTime $createdAt
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return \DateTime $createdAt
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }
}
