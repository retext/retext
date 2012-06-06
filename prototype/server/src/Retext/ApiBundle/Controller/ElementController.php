<?php

namespace Retext\ApiBundle\Controller;

use Retext\ApiBundle\RequestParamater, Retext\ApiBundle\Document\Project, Retext\ApiBundle\Document\Container;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
Symfony\Component\HttpFoundation\Response, Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class ElementController extends Base
{
    /**
     * Gibt eine Liste mit den Containern auf der obersten Ebene eines Projektes zurück
     *
     * @Route("/element", requirements={"_method":"GET"})
     */
    public function listElementAction()
    {
        $this->ensureLoggedIn();

        $dm = $this->get('doctrine.odm.mongodb.document_manager');
        $parent = $this->getContainer($this->getFromRequest(RequestParamater::create('parent')));
        $project = $parent->getProject();

        $elements = array();
        foreach (array('Container', 'Text') as $collection) {
            $collectionElements = $dm->getRepository('RetextApiBundle:' . $collection)
                ->createQueryBuilder()
                ->field('project')->equals(new \MongoId($project->getId()))
                ->field('parent')->equals(new \MongoId($parent->getId()))
                ->field('deletedAt')->exists(false)
                ->getQuery()
                ->execute();
            foreach ($collectionElements as $element) $elements[] = $element;
        }

        return $this->createListResponse($elements, $parent->getChildOrder());
    }

    /**
     * Gibt eine Liste mit allen Containern eines Projektes zurück
     *
     * @Route("/element/tree", requirements={"_method":"GET"})
     */
    public function listTreeAction()
    {
        $this->ensureLoggedIn();

        $dm = $this->get('doctrine.odm.mongodb.document_manager');
        $project = $this->getProject($this->getFromRequest(new RequestParamater('project')));

        // TODO: Implementiere das erstellen eine Baumes, bei dem die Elemente entsprechend des childOrder angeordnet sind.

        return $this->createListResponse(array());
    }
}
