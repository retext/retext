<?php

namespace Retext\ApiBundle\Model;

use Retext\ApiBundle\Exception\ValidationException;
use JMS\SerializerBundle\Annotation as SerializerBundle;

/**
 * Beschreibt einen Projektmitarbeiter
 *
 * @author Markus Tacker <m@tckr.cc>
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
     * @return string $id
     */
    public function getId()
    {
        return $this->getEmail();
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
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return string $email
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Gibt die Namen der verknüpften Dokumente zurück
     *
     * @return \Retext\ApiBundle\Model\DocumentRelation
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
