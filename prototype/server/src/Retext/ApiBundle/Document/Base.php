<?php

namespace Retext\ApiBundle\Document;

use JMS\SerializerBundle\Annotation as SerializerBundle;

abstract class Base implements LinkedData
{
    /**
     * @SerializerBundle\SerializedName("@context")
     */
    public $context;

    /**
     * @SerializerBundle\SerializedName("@subject")
     */
    public $subject;

    /**
     * @SerializerBundle\SerializedName("@relations")
     */
    public $relations;

    /**
     * Gibt den Context dieses Dokumentes zurück
     *
     * @return string
     * @SerializerBundle\PreSerialize
     */
    public function getContext()
    {

        $this->context = 'http://jsonld.retext.it/' . $this->getContextName();
        return $this->context;
    }

    /**
     * Gibt die URL (Subject) dieses Dokumentes zurück
     *
     * @return string
     * @SerializerBundle\PreSerialize
     */
    public function getSubject()
    {
        $this->subject = '/api/' . strtolower($this->getContextName());
        if (strlen($this->getId()) > 0) $this->subject .= '/' . $this->getId();
        return $this->subject;
    }

    /**
     * Generische Methode zum erzeugen des Context-Namens
     *
     * @return string
     */
    protected function getContextName()
    {
        $parts = explode('\\', get_class($this));
        return array_pop($parts);
    }

    /**
     * Gibt die mit diesem Dokument verknüpften Dokumente (Relations) zurück
     *
     * @return array
     * @SerializerBundle\PreSerialize
     */
    public function getRelations()
    {
        $this->relations = $this->getRelatedDocuments();
        if (empty($this->relations)) {
            $this->relations = null;
            return;
        }
    }
}
