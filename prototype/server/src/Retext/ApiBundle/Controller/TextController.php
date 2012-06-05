<?php

namespace Retext\ApiBundle\Controller;

use Retext\ApiBundle\RequestParamater, Retext\ApiBundle\Document\Project, Retext\ApiBundle\Document\Text, Retext\ApiBundle\Document\TextType;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
Symfony\Component\HttpFoundation\Response, Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class TextController extends Base
{
    /**
     * Legt einen neuen Text unterhalb eines Projektes an
     *
     * @Route("/text", requirements={"_method":"POST"})
     */
    public function createTextAction()
    {
        $this->ensureLoggedIn();

        $parent = $this->getContainer($this->getFromRequest(RequestParamater::create('parent')));
        $project = $parent->getProject();

        $dm = $this->get('doctrine.odm.mongodb.document_manager');
        $typeName = $this->getFromRequest(RequestParamater::create('type')->makeOptional()->defaultsTo('default'));
        $type = $dm->getRepository('RetextApiBundle:TextType')
            ->createQueryBuilder()
            ->field('project')->equals(new \MongoId($project->getId()))
            ->field('name')->equals($typeName)
            ->field('deletedAt')->exists(false)
            ->getQuery()
            ->getSingleResult();
        if ($type == null) {
            $type = new TextType();
            $type->setName($typeName);
            $type->setProject($project);
        }

        $text = new Text();
        $text->setProject($project);
        $text->setParent($parent);
        $text->setName($this->getFromRequest(RequestParamater::create('name')->makeOptional()->defaultsTo(null)));
        $text->setType($type);

        $dm->persist($text);
        $dm->flush();

        $this->addedChildElement($text);

        return $this->createResponse($text)->setStatusCode(201)->addHeader('Location', $text->getSubject());
    }

    /**
     * @Route("/text/{text_id}", requirements={"_method":"PUT"})
     */
    public function updateTextAction($text_id)
    {
        $this->ensureLoggedIn();

        $dm = $this->get('doctrine.odm.mongodb.document_manager');
        $text = $this->getText($text_id);

        // TODO: Check update permissions
        $text->setName($this->getFromRequest(RequestParamater::create('name')->makeOptional()->defaultsTo($text->getName())));

        $dm->persist($text);
        $dm->flush();

        return $this->createResponse($text);
    }

    /**
     * @Route("/text/{text_id}", requirements={"_method":"GET"})
     */
    public function getTextAction($text_id)
    {
        $this->ensureLoggedIn();
        $text = $this->getText($text_id);
        $response = $this->createResponse($text);
        return $response;
    }

    /**
     * @Route("/texttype", requirements={"_method":"GET"})
     */
    public function listTypesAction()
    {
        $this->ensureLoggedIn();
        $project = $this->getProject($this->getFromRequest(new RequestParamater('project')));
        $dm = $this->get('doctrine.odm.mongodb.document_manager');
        return $this->createListResponse($dm->getRepository('RetextApiBundle:TextType')
            ->createQueryBuilder()
            ->field('project')->equals(new \MongoId($project->getId()))
            ->field('deletedAt')->exists(false)
            ->getQuery()
            ->execute());
    }

    /**
     * @Route("/text/{text_id}", requirements={"_method":"DELETE"})
     */
    public function deleteTextAction($text_id)
    {
        $this->ensureLoggedIn();

        $text = $this->getText($text_id);

        // TODO: Check delete permissions
        $sdm = $this->get('doctrine.odm.mongodb.soft_delete.manager');
        $sdm->delete($text);
        $sdm->flush();

        $this->removedChildElement($text);

        return $this->createResponse();
    }
}
