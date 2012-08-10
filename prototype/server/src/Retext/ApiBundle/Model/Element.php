<?php

namespace Retext\ApiBundle\Model;

/**
 * Interface für Bausteine eines Produktes (Container und Texte)
 */
interface Element
{
    /**
     * Get id
     *
     * @return string $id
     */
    public function getId();

    /**
     * Get parent
     *
     * @return \Retext\ApiBundle\Document\Container $parent
     */
    public function getParent();
}
