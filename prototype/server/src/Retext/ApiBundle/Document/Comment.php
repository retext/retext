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
class Comment extends \Retext\ApiBundle\Model\Base implements \Doctrine\ODM\MongoDB\SoftDelete\SoftDeleteable
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
     * @var \Retext\ApiBundle\Document\Text $text
     * @SerializerBundle\Accessor(getter="getTextId")
     */
    private $text;

    /**
     * @MongoDB\ReferenceOne(targetDocument="Retext\ApiBundle\Document\User", cascade={"persist"}, simple=true)
     * @MongoDB\Index(order="asc")
     * @var \Retext\ApiBundle\Document\User $user
     * @SerializerBundle\Accessor(getter="getUserId")
     */
    private $user;

    /**
     * @MongoDB\Hash
     * @var object $userData
     * @SerializerBundle\SerializedName("user")
     */
    private $userData = array();

    /**
     * @MongoDB\String
     * @var int $text
     */
    private $comment = null;

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
     * @MongoDB\PrePersist
     * @MongoDB\PreUpdate
     */
    public function validate()
    {
        if (empty($this->project)) throw new ValidationException('project', 'empty');
        if (empty($this->text)) throw new ValidationException('text', 'empty');
        if (empty($this->comment)) throw new ValidationException('comment', 'empty');
        if (empty($this->user)) throw new ValidationException('user', 'empty');
        if (empty($this->userData)) throw new ValidationException('userData', 'empty');
        if (empty($this->createdAt)) throw new ValidationException('createdAt', 'empty');
    }

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
     * Gibt die Namen der verknüpften Dokumente zurück
     *
     * @return \Retext\ApiBundle\Model\DocumentRelation
     */
    public function getRelatedDocuments()
    {
        return array(
            \Retext\ApiBundle\Model\DocumentRelation::createFromDoc($this->getProject()),
            \Retext\ApiBundle\Model\DocumentRelation::createFromDoc($this->getText()),
            \Retext\ApiBundle\Model\DocumentRelation::createFromDoc($this->getUser()),
        );
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
     * Set text
     *
     * @param \Retext\ApiBundle\Document\Text $text
     */
    public function setText(\Retext\ApiBundle\Document\Text $text)
    {
        $this->text = $text;
    }

    /**
     * Get text
     *
     * @return \Retext\ApiBundle\Document\Text $text
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Get text id
     *
     * @return string
     */
    public function getTextId()
    {
        return $this->getText()->getId();
    }

    /**
     * Set user
     *
     * @param \Retext\ApiBundle\Document\User $user
     */
    public function setUser(\Retext\ApiBundle\Document\User $user)
    {
        $this->user = $user;
        $this->userData = array(
            'email' => $user->getEmail(),
            'emailmd5' => md5(strtolower(trim($user->getEmail()))) // Für Gravatar-URLs
        );
    }

    /**
     * Get user
     *
     * @return \Retext\ApiBundle\Document\User $user
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Get user id
     *
     * @return string
     */
    public function getUserId()
    {
        return $this->getUser()->getId();
    }

    /**
     * Set comment
     *
     * @param string $comment
     */
    public function setComment($comment)
    {
        $this->comment = trim($comment);
    }

    /**
     * Get comment
     *
     * @return string $comment
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Set createdAt
     *
     * @param date $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * Get createdAt
     *
     * @return date $createdAt
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set deletedAt
     *
     * @param date $deletedAt
     */
    public function setDeletedAt($deletedAt)
    {
        $this->deletedAt = $deletedAt;
    }

    /**
     * Set userData
     *
     * @param hash $userData
     */
    public function setUserData($userData)
    {
        $this->userData = $userData;
    }

    /**
     * Get userData
     *
     * @return hash $userData
     */
    public function getUserData()
    {
        return $this->userData;
    }
}
