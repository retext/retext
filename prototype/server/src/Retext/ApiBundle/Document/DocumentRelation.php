<?php

namespace Retext\ApiBundle\Document;

use JMS\SerializerBundle\Annotation as SerializerBundle;

class DocumentRelation
{
    /**
     * @SerializerBundle\SerializedName("@context")
     * @var string
     */
    private $context = "http://coderbyheart.de/jsonld/Relation";

    /**
     * @var string
     */
    private $relatedcontext;

    /**
     * @var string
     */
    private $role;

    /**
     * @var string
     */
    private $href;

    /**
     * @var boolean
     */
    private $list = false;

    /**
     * @param \Retext\ApiBundle\Document\Base $doc related document class
     * @static
     * @return DocumentRelation
     */
    public static function create(Base $doc)
    {
        return new DocumentRelation($doc);
    }

    /**
     * @param \Retext\ApiBundle\Document\Base $doc related document class
     * @static
     * @return DocumentRelation
     */
    public function __construct(Base $doc)
    {
        $this->setRelatedcontext($doc->getContext());
        $this->setHref($doc->getSubject());
    }

    /**
     * @param string $context
     * @return DocumentRelation
     */
    public function setContext($context)
    {
        $this->context = $context;
        return $this;
    }

    /**
     * @return string
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * @param string $href
     * @return DocumentRelation
     */
    public function setHref($href)
    {
        $this->href = $href;
        return $this;
    }

    /**
     * @return string
     */
    public function getHref()
    {
        return $this->href;
    }

    /**
     * @param boolean $list
     * @return DocumentRelation
     */
    public function setList($list)
    {
        $this->list = $list;
        return $this;
    }

    /**
     * @return boolean
     */
    public function getList()
    {
        return $this->list;
    }

    /**
     * @param string $relatedcontext
     * @return DocumentRelation
     */
    public function setRelatedcontext($relatedcontext)
    {
        $this->relatedcontext = $relatedcontext;
        return $this;
    }

    /**
     * @return string
     */
    public function getRelatedcontext()
    {
        return $this->relatedcontext;
    }

    /**
     * @param string $role
     * @return DocumentRelation
     */
    public function setRole($role)
    {
        $this->role = $role;
        return $this;
    }

    /**
     * @return string
     */
    public function getRole()
    {
        return $this->role;
    }
}
