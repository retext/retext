<?php

namespace Retext\ApiBundle\Document;

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
     * Gibt die Namen der verknüpften Dokumente zurück
     *
     * @return DocumentRelation[]|null
     */
    function getRelatedDocuments();
}
