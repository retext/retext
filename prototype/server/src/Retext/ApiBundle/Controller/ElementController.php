<?php

namespace Retext\ApiBundle\Controller;

use Retext\ApiBundle\RequestParamater, Retext\ApiBundle\Document\Project, Retext\ApiBundle\Document\Container;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
Symfony\Component\HttpFoundation\Response, Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class ElementController extends Base
{
    /**
     * Gibt eine Liste mit den Containern auf der obersten Ebene eines Projektes zurück, in der Reihenfolge, wie sie vom Nutzer festgelegt wurde
     *
     * @Route("/element", requirements={"_method":"GET"})
     */
    public function listElementAction()
    {
        $this->ensureLoggedIn();
        $parent = $this->getContainer($this->getFromRequest(RequestParamater::create('parent')));
        $exportContainerChildren = $this->get('retext.apibundle.export.containerchildren');
        return $this->createListResponse($exportContainerChildren->getChildren($parent));
    }

    /**
     * Gibt eine Liste mit allen Containern eines Projektes zurück
     *
     * @Route("/element/tree", requirements={"_method":"GET"})
     */
    public function listTreeAction()
    {
        // TODO: Implementiere das erstellen eine Baumes, bei dem die Elemente entsprechend des childOrder angeordnet sind.
        $this->ensureLoggedIn();
        return $this->createListResponse(array());
    }
}
