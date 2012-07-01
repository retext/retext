<?php

namespace Retext\ApiBundle\Model;

use Retext\ApiBundle\Exception\ValidationException;
use JMS\SerializerBundle\Annotation as SerializerBundle;

/**
 */
class ProjectContributor extends Base
{
    /**
     * @var \Retext\ApiBundle\Document\Project $project
     * @SerializerBundle\Accessor(getter="getProjectId")
     */
    private $project;

    /**
     * @var string $email
     */
    private $email;

    /**
     * @var string
     * @SerializerBundle\Accessor(getter="getEmail")
     */
    private $id;

    /**
     * Get id
     *
     * @return string $id
     */
    public function getId()
    {
        return $this->getEmail();
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
     * Set user
     *
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * Get user
     *
     * @return string $email
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Gibt die Namen der verknüpften Dokumente zurück
     *
     * @return DocumentRelation
     */
    public function getRelatedDocuments()
    {
        return array(
            DocumentRelation::createFromDoc($this->getProject())
        );
    }

    /**
     * Gibt die URL (Subject) dieses Dokumentes zurück
     *
     * @return string
     * @SerializerBundle\PreSerialize
     */
    public function getSubject()
    {
        $subject = $this->getProject()->getSubject() . '/contributor';
        if ($this->getEmail() !== null) $subject .= '/' . $this->getEmail();
        return $subject;
    }
}
