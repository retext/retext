<?php

namespace Retext\ApiBundle\Controller;

use Retext\ApiBundle\RequestParamater, Retext\ApiBundle\Document\Project, Retext\ApiBundle\Document\Container;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
Symfony\Component\HttpFoundation\Response, Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class ProjectController extends Base
{
    /**
     * @Route("/project", requirements={"_method":"POST"})
     */
    public function createProjectAction()
    {
        $this->ensureLoggedIn();

        $project = new Project();
        $project->setOwner($this->getUser());
        $project->setName($this->getFromRequest('name'));

        $dm = $this->get('doctrine.odm.mongodb.document_manager');
        $dm->persist($project);
        $dm->flush();

        $rootContainer = new Container();
        $rootContainer->setRootContainer(true);
        $rootContainer->setProject($project);
        $project->setRootContainer($rootContainer);
        $dm->persist($rootContainer);
        $dm->flush();

        return $this->createResponse($project)->setStatusCode(201)->addHeader('Location', $project->getSubject());
    }

    /**
     * @Route("/project/{id}", requirements={"_method":"GET"})
     */
    public function getProjectAction($id)
    {
        $this->ensureLoggedIn();
        $this->ensureRequest();

        $dm = $this->get('doctrine.odm.mongodb.document_manager');
        $project = $dm->getRepository('RetextApiBundle:Project')
            ->findOneBy(array('owner' => new \MongoId($this->getUser()->getId()), 'id' => $id));

        if (!$project) throw $this->createNotFoundException();
        return $this->createResponse($project);
    }


    /**
     * @Route("/project", requirements={"_method":"GET"})
     */
    public function listProjectAction()
    {
        $this->ensureLoggedIn();
        $this->ensureRequest();

        $dm = $this->get('doctrine.odm.mongodb.document_manager');
        $projects = $dm->getRepository('RetextApiBundle:Project')
            ->createQueryBuilder()
            ->field('owner')->equals(new \MongoId($this->getUser()->getId()))
            ->getQuery()
            ->execute();
        return $this->createListResponse($projects);
    }
}
