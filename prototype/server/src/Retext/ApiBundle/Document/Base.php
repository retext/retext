<?php

namespace Retext\ApiBundle\Document;

use JMS\SerializerBundle\Annotation as SerializerBundle;

class Base implements LinkedData
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
        $this->subject = '/api/' . strtolower($this->getContextName()) . '/' . $this->getId();
        return $this->subject;
    }

    private function getContextName()
    {
        $parts = explode('\\', get_class($this));
        return array_pop($parts);
    }
}
