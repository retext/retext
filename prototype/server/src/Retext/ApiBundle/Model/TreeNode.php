<?php
namespace Retext\ApiBundle\Model;

use Retext\ApiBundle\Model\Element;

class TreeNode extends Base
{
    /**
     * @var \Retext\ApiBundle\Model\Element[]
     */
    public $children = array();

    /**
     * @var \Retext\ApiBundle\Model\Element
     */
    public $data;

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
