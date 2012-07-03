<?php

namespace Retext\ApiBundle\Document;

use Retext\ApiBundle\Exception\ValidationException;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\ORM\Mapping as Doctrine;
use JMS\SerializerBundle\Annotation as SerializerBundle;

/**
 * Repräsentiert Klassen von Texten.
 *
 * @author Markus Tacker <m@tckr.cc>
 *
 * @MongoDB\Document
 * @MongoDB\UniqueIndex(keys={"project"="asc", "name"="asc"})
 * @Doctrine\HasLifecycleCallbacks
 */
class TextType extends \Retext\ApiBundle\Model\Base implements \Doctrine\ODM\MongoDB\SoftDelete\SoftDeleteable
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
     * @MongoDB\String
     * @MongoDB\Index(order="asc")
     * @var string
     */
    protected $name;

    /**
     * @MongoDB\String
     * @var string
     */
    protected $description;

    /**
     * @MongoDB\Boolean
     * @var boolean
     */
    protected $multiline = false;

    /**
     * @MongoDB\Int
     * @var int
     */
    protected $fontsize;

    /**
     * @MongoDB\String
     * @var int
     */
    protected $fontname;

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
     * Gibt die Namen der verknüpften Dokumente zurück
     *
     * @return \Retext\ApiBundle\Model\DocumentRelation
     */
    public function getRelatedDocuments()
    {
        return array(
            \Retext\ApiBundle\Model\DocumentRelation::createFromDoc($this->getProject()),
        );
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return string $description
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param int $fontsize
     */
    public function setFontsize($fontsize)
    {
        $this->fontsize = $fontsize;
    }

    /**
     * @return int $fontsize
     */
    public function getFontsize()
    {
        return $this->fontsize;
    }

    /**
     * @param string $fontname
     */
    public function setFontname($fontname)
    {
        $this->fontname = $fontname;
    }

    /**
     * @return string $fontname
     */
    public function getFontname()
    {
        return $this->fontname;
    }

    /**
     * @param boolean $multiline
     */
    public function setMultiline($multiline)
    {
        $this->multiline = (bool)$multiline;
    }

    /**
     * @return boolean $multiline
     */
    public function getMultiline()
    {
        return $this->multiline;
    }
}
