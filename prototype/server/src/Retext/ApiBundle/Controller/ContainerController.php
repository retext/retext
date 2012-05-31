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
     * @Route("/project/{id}/container", requirements={"_method":"POST"})
     */
    public function createContainerAction($id)
    {
        $this->ensureLoggedIn();

        $dm = $this->get('doctrine.odm.mongodb.document_manager');
        $project = $dm->getRepository('RetextApiBundle:Project')
            ->findOneBy(array('owner.$id' => new \MongoId($this->getUser()->getId()), 'id' => $id));

        $numContainer = $dm->getRepository('RetextApiBundle:Container')
            ->createQueryBuilder()
            ->field('project.$id')->equals(new \MongoId($project->getId()))
            ->field('parent')->equals(null)
            ->count()
            ->getQuery()
            ->execute();

        $container = new Container();
        $container->setProject($project);
        $container->setName($this->getFromRequest(RequestParamater::create('name')->makeOptional()->defaultsTo(null)));
        $container->setOrder($numContainer + 1);

        $dm->persist($container);
        $dm->flush();

        return $this->createResponse($container)->setStatusCode(201)->addHeader('Location', $container->getSubject());
    }

    /**
     * @Route("/project/{id}/container", requirements={"_method":"GET"})
     */
    public function listContainerAction($id)
    {
        $this->ensureLoggedIn();

        $dm = $this->get('doctrine.odm.mongodb.document_manager');
        $project = $dm->getRepository('RetextApiBundle:Project')
            ->findOneBy(array('owner.$id' => new \MongoId($this->getUser()->getId()), 'id' => $id));

        $container = $dm->getRepository('RetextApiBundle:Container')
            ->createQueryBuilder()
            ->field('project.$id')->equals(new \MongoId($project->getId()))
            ->getQuery()
            ->execute();

        return $this->createListResponse($container);
    }

    /**
     * @Route("/project/{project_id}/container/{container_id}", requirements={"_method":"PUT"})
     */
    public function updateContainerAction($project_id, $container_id)
    {
        $this->ensureLoggedIn();

        $dm = $this->get('doctrine.odm.mongodb.document_manager');
        $container = $dm->getRepository('RetextApiBundle:Container')
            ->findOneBy(array('id' => $container_id));

        // TODO: Check update permissions
        $container->setName($this->getFromRequest(RequestParamater::create('name')->makeOptional()->defaultsTo($container->getName())));

        // Updating order?
        $newOrder = $this->getFromRequest(RequestParamater::create('order')->makeOptional()->makeInteger()->defaultsTo($container->getOrder()));
        if ($newOrder != $container->getOrder()) {
            // Make room for new order
            $dm->getRepository('RetextApiBundle:Container')
                ->createQueryBuilder()
                ->findAndUpdate()
                ->field('project.$id')->equals(new \MongoId($project_id))
                ->field('order')->lte($newOrder)->inc(1)
                ->getQuery()
                ->execute();
            $container->setOrder($newOrder);
        }
        $dm->persist($container);
        $dm->flush();

        return $this->createResponse($container);
    }

    /**
     * @Route("/project/{project_id}/container/{container_id}", requirements={"_method":"GET"})
     */
    public function getContainerAction($project_id, $container_id)
    {
        $this->ensureLoggedIn();

        $dm = $this->get('doctrine.odm.mongodb.document_manager');
        $container = $dm->getRepository('RetextApiBundle:Container')
            ->findOneBy(array('id' => $container_id, 'project.$id' => new \MongoId($project_id)));

        return $this->createResponse($container);
    }
}
