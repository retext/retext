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
class Project extends \Retext\ApiBundle\Model\Base implements \Doctrine\ODM\MongoDB\SoftDelete\SoftDeleteable
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
     * @MongoDB\Hash
     * @var string[] $contributors E-mail addresses of contributors
     * @MongoDB\Index(order="asc")
     * @SerializerBundle\Exclude
     */
    private $contributors = array();

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
     * @MongoDB\String
     * @var string
     * @SerializerBundle\SerializedName("defaultLanguage")
     */
    private $defaultLanguage;

    /**
     * @param string $id
     */
    public function setId($id)
    {
        $this->id = $id;
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
        if (empty($this->defaultLanguage)) throw new ValidationException('defaultLanguage', 'empty');
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
     * Setzt die Mitarbeiter des Projekts
     *
     * @param string[] $emails Liste mit E-Mail-Adressen
     */
    public function setContributors($emails)
    {
        $this->contributors = $emails;
    }

    /**
     * Gibt die E-Mail-Adressen der Mitarbeiter eines Projekts zurück
     *
     * @return string[]
     */
    public function getContributors()
    {
        return $this->contributors;
    }

    /**
     * Fügt dem Projekt einen Mitarbeiter hinzu
     *
     * @param string $emails
     */
    public function addContributor($email)
    {
        $this->contributors[] = trim($email);
        $this->contributors = array_unique($this->contributors, SORT_STRING);
    }

    /**
     * Entfernt einen Mitarbeiter aus dem Projekt
     *
     * @param string $email
     */
    public function removeContributor($email)
    {
        if (($pos = array_search($email, $this->contributors)) !== false) {
            unset($this->contributors[$pos]);
        }
    }

    /**
     * Gibt die Namen der verknüpften Dokumente zurück
     *
     * @return \Retext\ApiBundle\Model\DocumentRelation[]|null
     */
    public function getRelatedDocuments()
    {
        $rootContainer = $this->getRootContainer();
        $textType = new TextType();
        $progress = new \Retext\ApiBundle\Model\ProjectProgress();
        $contributors = new \Retext\ApiBundle\Model\ProjectContributor();
        $contributors->setProject($this);
        $languages = new Language();
        $languages->setProject($this);
        return array(
            \Retext\ApiBundle\Model\DocumentRelation::createFromDoc($rootContainer)->setHref($rootContainer->getSubject())->setRole('http://jsonld.retext.it/ontology/root'),
            \Retext\ApiBundle\Model\DocumentRelation::createFromDoc($textType)->setHref($textType->getSubject() . '?project=' . $this->getId())->setList(true),
            \Retext\ApiBundle\Model\DocumentRelation::createFromDoc($progress)->setHref($this->getSubject() . '/progress'),
            \Retext\ApiBundle\Model\DocumentRelation::createFromDoc($contributors)->setList(true),
            \Retext\ApiBundle\Model\DocumentRelation::createFromDoc($languages)->setList(true),
            \Retext\ApiBundle\Model\DocumentRelation::create()->setRelatedcontext('http://jsonld.retext.it/Element')->setList(true)->setRole('http://jsonld.retext.it/ontology/tree')->setHref($rootContainer->getSubject() . '/tree'),
        );
    }

    /**
     * @param string $defaultLanguage
     */
    public function setDefaultLanguage($defaultLanguage)
    {
        $this->defaultLanguage = $defaultLanguage;
    }

    /**
     * @return string
     */
    public function getDefaultLanguage()
    {
        return $this->defaultLanguage;
    }
}
