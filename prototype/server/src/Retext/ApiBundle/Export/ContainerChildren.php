<?php

namespace Retext\ApiBundle\Export;

use Retext\ApiBundle\Document\Project, Retext\ApiBundle\Document\Container;

use Doctrine\ODM\MongoDB\DocumentManager;

/**
 * Exportiert die Kind-Elemente eines Containers.
 *
 * @author Markus Tacker <m@tckr.cc>
 */
class ContainerChildren
{
    /**
     * @var \Doctrine\ODM\MongoDB\DocumentManager
     */
    private $dm;

    public function __construct(DocumentManager $dm)
    {
        $this->dm = $dm;
    }

    /**
     * Gibt eine Liste mit den Containern auf der obersten Ebene eines Projektes zurück, in der Reihenfolge, wie sie vom Nutzer festgelegt wurde
     *
     * @param Container $parent
     * @return \Retext\ApiBundle\Document\Element[]
     */
    public function getChildren(Container $parent)
    {
        $project = $parent->getProject();

        $elements = array();
        foreach (array('Container', 'Text') as $collection) {
            $qb = $this->dm->getRepository('RetextApiBundle:' . $collection)
                ->createQueryBuilder();
            $qb->field('project')->equals(new \MongoId($project->getId()))
                ->field('parent')->equals(new \MongoId($parent->getId()))
                ->field('deletedAt')->exists(false);
            $collectionElements = $qb
                ->getQuery()
                ->execute();
            foreach ($collectionElements as $element) $elements[] = $element;
        }

        return $this->getOrdered($elements, $parent->getChildOrder());
    }

    protected function getOrdered($elements, $order)
    {
        $items = array();
        $itemPos = array();
        $idPos = array_flip($order);
        foreach ($elements as $d) {
            $items[] = $d;
            $itemPos[] = $idPos[$d->getId()];
        }
        array_multisort($itemPos, SORT_ASC, $items);
        return $items;
    }
}
