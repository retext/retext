<?php

namespace Retext\ApiBundle\Model;

use JMS\SerializerBundle\Annotation as SerializerBundle;

/**
 * Definiert Beziehungen zwischen Dokumenten. Wird für die API verwendet.
 *
 * @author Markus Tacker <m@tckr.cc>
 */
class DocumentRelation
{
    /**
     * Bezeichnet den Kontext dieses Objekts
     *
     * @SerializerBundle\SerializedName("@context")
     * @var string
     */
    private $context = "http://coderbyheart.de/jsonld/Relation";

    /**
     * Der Kontext des "anderen" Objekts
     *
     * @var string
     */
    private $relatedcontext;

    /**
     * Die Rolle der Beziehung
     *
     * @var string
     */
    private $role;

    /**
     * Der Link zum Laden des "anderen" Objekts
     *
     * @var string
     */
    private $href;

    /**
     * Wahr, wenn es sich bei der Beziehung um eine Liste von Objekten handelt
     *
     * @var boolean
     */
    private $list = false;

    /**
     * Erzeugt eine neue Beziehung auf Basis eines Dokument
     *
     * @param \Retext\ApiBundle\Model\Base $doc
     * @static
     * @return \Retext\ApiBundle\Model\DocumentRelation $doc
     */
    public static function createFromDoc(Base $doc)
    {
        $rel = new DocumentRelation();
        $rel->setRelatedcontext($doc->getContext());
        $rel->setHref($doc->getSubject());
        return $rel;
    }

    /**
     * Erzeugt eine neue Beziehung
     *
     * @static
     * @return \Retext\ApiBundle\Model\DocumentRelation
     */
    public static function create()
    {
        return new DocumentRelation();
    }

    /**
     * Setzt den Kontext des "anderen" Objekts
     *
     * @param string $context
     * @return DocumentRelation
     */
    public function setContext($context)
    {
        $this->context = $context;
        return $this;
    }

    /**
     * Gibt den Kontext des "anderen" Objekts
     *
     * @return string
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * Setzt den Link zum Laden des "anderen" Objekts
     * @param string $href
     * @return DocumentRelation
     */
    public function setHref($href)
    {
        $this->href = $href;
        return $this;
    }

    /**
     * Gibt den Link zum Laden des "anderen" Objekts zurück
     *
     * @return string
     */
    public function getHref()
    {
        return $this->href;
    }

    /**
     * Setzt, ob es sich bei der Beziehung um eine Liste von Objekten handelt
     *
     * @param boolean $list
     * @return DocumentRelation
     */
    public function setList($list)
    {
        $this->list = (bool)$list;
        return $this;
    }

    /**
     * Gibt zurück, ob es sich bei der Beziehung um eine Liste von Objekten handelt
     *
     * @return boolean
     */
    public function getList()
    {
        return $this->list;
    }

    /**
     * Setzt den Kontext des "anderen" Objekts
     *
     * @param string $relatedcontext
     * @return DocumentRelation
     */
    public function setRelatedcontext($relatedcontext)
    {
        $this->relatedcontext = $relatedcontext;
        return $this;
    }

    /**
     * Gibt den Kontext des "anderen" Objekts zurück
     *
     * @return string
     */
    public function getRelatedcontext()
    {
        return $this->relatedcontext;
    }

    /**
     * Setzt die Rolle der Beziehung
     *
     * @param string $role
     * @return DocumentRelation
     */
    public function setRole($role)
    {
        $this->role = $role;
        return $this;
    }

    /**
     * Gibt die Rolle der Beziehung zurück
     *
     * @return string
     */
    public function getRole()
    {
        return $this->role;
    }
}
