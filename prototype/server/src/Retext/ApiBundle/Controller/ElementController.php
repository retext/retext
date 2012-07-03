<?php

namespace Retext\ApiBundle\Controller;

use Retext\ApiBundle\Controller\RequestParameter, Retext\ApiBundle\Document\Project, Retext\ApiBundle\Document\Container;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
Symfony\Component\HttpFoundation\Response, Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * Controller für die Elemente (Container und Texte)
 *
 * @author Markus Tacker <m@tckr.cc>
 */
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
        $parent = $this->getContainer($this->getFromRequest(RequestParameter::create('parent')));
        $exportContainerChildren = $this->get('retext.apibundle.export.containerchildren');
        return $this->createListResponse($exportContainerChildren->getChildren($parent));
    }
}
