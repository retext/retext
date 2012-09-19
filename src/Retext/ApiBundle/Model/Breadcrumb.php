<?php

namespace Retext\ApiBundle\Model;

use JMS\SerializerBundle\Annotation as SerializerBundle;

/**
 * Repräsentiert einen Eintrag in der Bread-Crumb-Navigation
 *
 * @author Markus Tacker <m@tckr.cc>
 */
class Breadcrumb extends Base
{
    private $id;
    private $name;

    public function __create(Container $container)
    {
        $this->name = $container->getName();
        $this->id = $container->getId();
    }

    /**
     * Gibt die Namen der verknüpften Dokumente zurück
     *
     * @return DocumentRelation[]|null
     */
    public function getRelatedDocuments()
    {
        return null;
    }

    /**
     * Gibt die ID dieses Dokumentes zurück
     *
     * @return string
     */
    function getId()
    {
        return $this->id;
    }
}
