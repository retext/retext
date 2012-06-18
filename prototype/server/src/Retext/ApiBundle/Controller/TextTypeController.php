<?php

namespace Retext\ApiBundle\Controller;

use Retext\ApiBundle\Controller\RequestParameter, Retext\ApiBundle\Document\Project, Retext\ApiBundle\Document\Text, Retext\ApiBundle\Document\TextType;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
Symfony\Component\HttpFoundation\Response, Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class TextTypeController extends Base
{

    /**
     * @Route("/texttype/{id}", requirements={"_method":"GET"})
     */
    public function getTypeAction($id)
    {
        $this->ensureLoggedIn();
        $text = $this->getTextType($id);
        $response = $this->createResponse($text);
        return $response;
    }

    /**
     * @Route("/texttype", requirements={"_method":"GET"})
     */
    public function listTypesAction()
    {
        $this->ensureLoggedIn();
        $project = $this->getProject($this->getFromRequest(new RequestParameter('project')));
        $dm = $this->get('doctrine.odm.mongodb.document_manager');
        return $this->createListResponse($dm->getRepository('RetextApiBundle:TextType')
            ->createQueryBuilder()
            ->field('project')->equals(new \MongoId($project->getId()))
            ->field('deletedAt')->exists(false)
            ->getQuery()
            ->execute());
    }

    /**
     * @Route("/texttype/{id}", requirements={"_method":"PUT"})
     */
    public function updateTypeAction($id)
    {
        $this->ensureLoggedIn();

        $dm = $this->get('doctrine.odm.mongodb.document_manager');
        $texttype = $this->getTextType($id);

        // TODO: Check update permissions
        $texttype->setName($this->getFromRequest(RequestParameter::create('name')->makeOptional()->defaultsTo($texttype->getName())));
        $texttype->setFontsize($this->getFromRequest(RequestParameter::create('fontsize')->makeOptional()->makeInteger()->defaultsTo($texttype->getFontsize())));
        $texttype->setFontname($this->getFromRequest(RequestParameter::create('fontname')->makeOptional()->defaultsTo($texttype->getFontname())));
        $texttype->setDescription($this->getFromRequest(RequestParameter::create('description')->makeOptional()->defaultsTo($texttype->getDescription())));
        $texttype->setMultiline($this->getFromRequest(RequestParameter::create('multiline')->makeOptional()->makeBoolean()->defaultsTo($texttype->getMultiline())));

        $dm->persist($texttype);
        $dm->flush();

        return $this->createResponse($texttype);
    }
}
