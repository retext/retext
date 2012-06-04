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

        list($container, $project) = $this->getContainerAndProject();

        $dm = $this->get('doctrine.odm.mongodb.document_manager');
        $numTexts = $dm->getRepository('RetextApiBundle:Container')
            ->createQueryBuilder()
            ->field('project')->equals(new \MongoId($project->getId()))
            ->field('container')->equals(new \MongoId($container->getId()))
            ->count()
            ->getQuery()
            ->execute();

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
        $text->setContainer($container);
        $text->setName($this->getFromRequest(RequestParamater::create('name')->makeOptional()->defaultsTo(null)));
        $text->setOrder($numTexts + 1);
        $text->setType($type);

        $dm->persist($text);
        $dm->flush();

        return $this->createResponse($text)->setStatusCode(201)->addHeader('Location', $text->getSubject());
    }

    /**
     * Gibt das Projekt und den Container zurück.
     *
     * @return array
     */
    protected function getContainerAndProject()
    {
        $container = $this->getContainer($this->getFromRequest(RequestParamater::create('container')));
        return array($container, $container->getProject());
    }

    /**
     * Gibt eine Liste mit den Texten auf der obersten Ebene eines Projektes zurück
     *
     * @Route("/text", requirements={"_method":"GET"})
     */
    public function listTextAction()
    {
        $this->ensureLoggedIn();

        $dm = $this->get('doctrine.odm.mongodb.document_manager');

        list($container, $project) = $this->getContainerAndProject();

        $query = $dm->getRepository('RetextApiBundle:Text')
            ->createQueryBuilder()
            ->field('project')->equals(new \MongoId($project->getId()))
            ->field('container')->equals(new \MongoId($container->getId()))
            ->field('deletedAt')->exists(false)
            ->sort('order', 'asc')
            ->getQuery();
        $text = $query->execute();

        return $this->createListResponse($text);
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

        // Updating order?
        $newOrder = $this->getFromRequest(RequestParamater::create('order')->makeOptional()->makeInteger()->defaultsTo($text->getOrder()));
        if ($newOrder != $text->getOrder()) {
            // Make room for new order
            $dm->getRepository('RetextApiBundle:Text')
                ->createQueryBuilder()
                ->findAndUpdate()
                ->field('project')->equals($text->getProject()->getId())
                ->field('container')->equals($text->getContainer()->getId())
                ->field('order')->equals($newOrder)->set($text->getOrder())
                ->getQuery()
                ->execute();
            $text->setOrder($newOrder);
        }

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

        return $this->createResponse();
    }
}
