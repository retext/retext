<?php
namespace Retext\ApiBundle\Model;

use Retext\ApiBundle\Model\Element;

/**
 * Model zum Erzeugen eines Baumes mit allen Elementen eines Projekts
 *
 * @author Markus Tacker <m@tckr.cc>
 */
class TreeNode extends Base
{
    /**
     * Die Kind-Elemente dieses Knotens
     *
     * @var \Retext\ApiBundle\Model\Element[]
     */
    public $children = array();

    /**
     * Der eigentliche Knoten
     *
     * @var \Retext\ApiBundle\Model\Element
     */
    public $data;

    /**
     * Erzeugt einen neuen Knoten
     *
     * @param Retext\ApiBundle\Model\Element $data
     */
    public function __construct(Element $data)
    {
        $this->data = $data;
    }

    /**
     * Gibt die ID dieses Dokumentes zurück
     *
     * @return string
     */
    function getId()
    {
        return null;
    }

    /**
     * Gibt die Namen der verknüpften Dokumente zurück
     *
     * @return DocumentRelation[]|null
     */
    function getRelatedDocuments()
    {
        return null;
    }
}
