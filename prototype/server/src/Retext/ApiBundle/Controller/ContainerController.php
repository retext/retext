<?php

namespace Retext\ApiBundle\Controller;

use Retext\ApiBundle\RequestParamater, Retext\ApiBundle\Document\Project, Retext\ApiBundle\Document\Container;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
Symfony\Component\HttpFoundation\Response, Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class ContainerController extends Base
{
    /**
     * Legt einen neuen Container unterhalb eines Projektes an
     *
     * @Route("/container", requirements={"_method":"POST"})
     */
    public function createContainerAction()
    {
        $this->ensureLoggedIn();

        list($parent, $project) = $this->getProjectAndParent();

        $dm = $this->get('doctrine.odm.mongodb.document_manager');
        $numContainer = $dm->getRepository('RetextApiBundle:Container')
            ->createQueryBuilder()
            ->field('project')->equals(new \MongoId($project->getId()))
            ->field('parent')->equals($parent == null ? null : new \MongoId($parent->getId()))
            ->count()
            ->getQuery()
            ->execute();

        $container = new Container();
        $container->setProject($project);
        if ($parent !== null) $container->setParent($parent);
        $container->setName($this->getFromRequest(RequestParamater::create('name')->makeOptional()->defaultsTo(null)));
        $container->setOrder($numContainer + 1);

        $dm->persist($container);
        $dm->flush();

        return $this->createResponse($container)->setStatusCode(201)->addHeader('Location', $container->getSubject());
    }

    /**
     * Gibt das Projekt und den Eltern-Container zurück. Wird parent angegeben, wird das Projekt von dort übernommen.
     * Wird parent nicht angegeben, wird das Projekt aus dem Request genommen.
     *
     * @return array
     */
    protected function getProjectAndParent()
    {
        $projectId = null;
        $parentId = $this->getFromRequest(RequestParamater::create('parent')->makeOptional());
        $parent = null;
        if ($parentId) {
            $parent = $this->getContainer($parentId);
        }
        $project = null;
        if ($parent) {
            $project = $parent->getProject();
        } else {
            $project = $this->getProject($this->getFromRequest(RequestParamater::create('project')));
        }
        return array($parent, $project);
    }

    /**
     * Gibt eine Liste mit den Containern auf der obersten Ebene eines Projektes zurück
     *
     * @Route("/container", requirements={"_method":"GET"})
     */
    public function listContainerAction()
    {
        $this->ensureLoggedIn();

        $dm = $this->get('doctrine.odm.mongodb.document_manager');

        list($parent, $project) = $this->getProjectAndParent();

        $query = $dm->getRepository('RetextApiBundle:Container')
            ->createQueryBuilder()
            ->field('project')->equals(new \MongoId($project->getId()))
            ->field('parent')->equals($parent == null ? null : new \MongoId($parent->getId()))
            ->field('deletedAt')->exists(false)
            ->sort('order', 'asc')
            ->getQuery();
        $container = $query->execute();

        return $this->createListResponse($container);
    }

    /**
     * @Route("/container/{container_id}", requirements={"_method":"PUT"})
     */
    public function updateContainerAction($container_id)
    {
        $this->ensureLoggedIn();

        $dm = $this->get('doctrine.odm.mongodb.document_manager');
        $container = $this->getContainer($container_id);

        // TODO: Check update permissions
        $container->setName($this->getFromRequest(RequestParamater::create('name')->makeOptional()->defaultsTo($container->getName())));

        // Updating order?
        $newOrder = $this->getFromRequest(RequestParamater::create('order')->makeOptional()->makeInteger()->defaultsTo($container->getOrder()));
        if ($newOrder != $container->getOrder()) {
            // Make room for new order
            $dm->getRepository('RetextApiBundle:Container')
                ->createQueryBuilder()
                ->findAndUpdate()
                ->field('project')->equals($container->getProject()->getId())
                ->field('order')->equals($newOrder)->set($container->getOrder())
                ->getQuery()
                ->execute();
            $container->setOrder($newOrder);
        }

        $dm->persist($container);
        $dm->flush();

        return $this->createResponse($container);
    }

    /**
     * @param string $container_id
     * @return \Retext\ApiBundle\Document\Container
     */
    protected function getContainer($container_id)
    {
        $dm = $this->get('doctrine.odm.mongodb.document_manager');
        $container = $dm->getRepository('RetextApiBundle:Container')
            ->createQueryBuilder()
            ->field('id')->equals(new \MongoId($container_id))
            ->getQuery()
            ->getSingleResult();

        if ($container === null)
            throw $this->createNotFoundException('Container ' . $container_id . ' not found.');
        if ($container->getDeletedAt() !== null)
            throw $this->createGoneException('Container ' . $container_id . ' has been deleted.');
        return $container;
    }

    /**
     * @param string $project_id
     * @return \Retext\ApiBundle\Document\Project
     */
    protected function getProject($project_id)
    {
        $dm = $this->get('doctrine.odm.mongodb.document_manager');
        $project = $dm->getRepository('RetextApiBundle:Project')
            ->createQueryBuilder()
            ->field('id')->equals(new \MongoId($project_id))
            ->field('owner')->equals(new \MongoId($this->getUser()->getId()))
            ->getQuery()
            ->getSingleResult();
        if ($project === null)
            throw $this->createNotFoundException('Project ' . $project_id . ' not found.');
        if ($project->getDeletedAt() !== null)
            throw $this->createGoneException('Project ' . $project_id . ' has been deleted.');
        return $project;
    }

    /**
     * @Route("/container/{container_id}", requirements={"_method":"GET"})
     */
    public function getContainerAction($container_id)
    {
        $this->ensureLoggedIn();
        $container = $this->getContainer($container_id);
        $response = $this->createResponse($container);
        return $response;
    }

    /**
     * @Route("/container/{container_id}", requirements={"_method":"DELETE"})
     */
    public function deleteContainerAction($container_id)
    {
        $this->ensureLoggedIn();

        $container = $this->getContainer($container_id);

        // TODO: Check delete permissions
        $sdm = $this->get('doctrine.odm.mongodb.soft_delete.manager');
        $sdm->delete($container);
        $sdm->flush();

        return $this->createResponse();
    }
}
