<?php

namespace Retext\ApiBundle\Model;

/**
 * Interface für mit Beziehungen verbundenen Models
 *
 * @author Markus Tacker <m@tckr.cc>
 */
interface LinkedData
{
    /**
     * Gibt den Context dieses Dokumentes zurück
     *
     * @abstract
     * @return string
     */
    function getContext();

    /**
     * Gibt die URL (Subject) dieses Dokumentes zurück
     *
     * @abstract
     * @return string
     */
    function getSubject();

    /**
     * Gibt die ID dieses Dokumentes zurück
     *
     * @abstract
     * @return string
     */
    function getId();

    /**
     * Gibt die Namen der verknüpften Dokumente zurück
     *
     * @return DocumentRelation[]|null
     */
    function getRelatedDocuments();
}
