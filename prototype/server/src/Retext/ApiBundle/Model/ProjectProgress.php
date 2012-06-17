<?php

namespace Retext\ApiBundle\Model;

use Retext\ApiBundle\Exception\ValidationException;
use JMS\SerializerBundle\Annotation as SerializerBundle;

/**
 */
class ProjectProgress extends Base
{
    /**
     * @var \Retext\ApiBundle\Document\Project $project
     * @SerializerBundle\Accessor(getter="getProjectId")
     */
    private $project;

    /**
     * Rechtschreibung in Ordnung
     *
     * @SerializerBundle\SerializedName("spellingApproved")
     * @var object
     */
    private $spellingApproved;

    /**
     * Inhalt in Ordnung
     *
     * @SerializerBundle\SerializedName("contentApproved")
     * @var object
     */
    private $contentApproved;

    /**
     * Freigabe erteilt
     *
     * @var object
     */
    private $approved;

    /**
     * Gesamtstatus
     *
     * @var object
     */
    private $total;

    /**
     * Get id
     *
     * @return string $id
     */
    public function getId()
    {
        return null;
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
     * Set spellingApproved
     *
     * @param object $spellingApproved
     */
    public function setSpellingApproved($spellingApproved)
    {
        $this->spellingApproved = $spellingApproved;
    }

    /**
     * Get spellingApproved
     *
     * @return object $spellingApproved
     */
    public function getSpellingApproved()
    {
        return $this->spellingApproved;
    }

    /**
     * Set contentApproved
     *
     * @param object $contentApproved
     */
    public function setContentApproved($contentApproved)
    {
        $this->contentApproved = $contentApproved;
    }

    /**
     * Get contentApproved
     *
     * @return object $contentApproved
     */
    public function getContentApproved()
    {
        return $this->contentApproved;
    }

    /**
     * Set approved
     *
     * @param object $approved
     */
    public function setApproved($approved)
    {
        $this->approved = $approved;
    }

    /**
     * Get approved
     *
     * @return object $approved
     */
    public function getApproved()
    {
        return $this->approved;
    }

    /**
     * Set total
     *
     * @param object $total
     */
    public function setTotal($total)
    {
        $this->total = $total;
    }

    /**
     * Get total
     *
     * @return object $total
     */
    public function getTotal()
    {
        return $this->total;
    }
}
