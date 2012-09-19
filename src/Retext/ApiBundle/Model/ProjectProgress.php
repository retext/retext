<?php

namespace Retext\ApiBundle\Model;

use Retext\ApiBundle\Exception\ValidationException;
use JMS\SerializerBundle\Annotation as SerializerBundle;

/**
 * Beschriebt den Fortschritt eines Projekts
 *
 * @author Markus Tacker <m@tckr.cc>
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
     * @return string $id
     */
    public function getId()
    {
        return null;
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
     * @param object $spellingApproved
     */
    public function setSpellingApproved($spellingApproved)
    {
        $this->spellingApproved = $spellingApproved;
    }

    /**
     * @return object $spellingApproved
     */
    public function getSpellingApproved()
    {
        return $this->spellingApproved;
    }

    /**
     * @param object $contentApproved
     */
    public function setContentApproved($contentApproved)
    {
        $this->contentApproved = $contentApproved;
    }

    /**
     * @return object $contentApproved
     */
    public function getContentApproved()
    {
        return $this->contentApproved;
    }

    /**
     * @param object $approved
     */
    public function setApproved($approved)
    {
        $this->approved = $approved;
    }

    /**
     * @return object $approved
     */
    public function getApproved()
    {
        return $this->approved;
    }

    /**
     * @param object $total
     */
    public function setTotal($total)
    {
        $this->total = $total;
    }

    /**
     * @return object $total
     */
    public function getTotal()
    {
        return $this->total;
    }
}
