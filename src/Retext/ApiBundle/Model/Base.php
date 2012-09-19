<?php

namespace Retext\ApiBundle\Model;

use JMS\SerializerBundle\Annotation as SerializerBundle;

/**
 * Basisklasse für alle Models
 *
 * @author Markus Tacker <m@tckr.cc>
 */
abstract class Base implements LinkedData
{
    /**
     * @SerializerBundle\SerializedName("@context")
     * @SerializerBundle\Accessor(getter="getContext")
     */
    public $context;

    /**
     * @SerializerBundle\SerializedName("@subject")
     * @SerializerBundle\Accessor(getter="getSubject")
     */
    public $subject;

    /**
     * @SerializerBundle\SerializedName("@relations")
     * @SerializerBundle\Accessor(getter="getRelatedDocuments")
     */
    public $relations;

    /**
     * Gibt den Context dieses Dokumentes zurück
     *
     * @return string
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
     */
    public function getSubject()
    {
        $this->subject = '/' . strtolower($this->getContextName());
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
}
