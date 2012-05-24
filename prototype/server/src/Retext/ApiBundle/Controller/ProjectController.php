<?php

namespace Retext\ApiBundle\Controller;

use Retext\ApiBundle\Document\Project;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
Symfony\Component\HttpFoundation\Response, Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class ProjectController extends Base
{
    /**
     * @Route("/project", requirements={"_method":"POST"})
     */
    public function createProjectAction(Request $request)
    {
        $this->ensureLoggedIn();

        $project = new Project();
        $project->setOwner($this->getUser());
        if ($request->headers->get('Content-Type') == 'application/json') {
            $data = json_decode($request->getContent());
            $project->setName($data->name);
        } else {
            $project->setName($request->get('name'));
        }

        $dm = $this->get('doctrine.odm.mongodb.document_manager');
        $dm->persist($project);
        $dm->flush();

        return $this->createResponse($project)->setStatusCode(201)->addHeader('Location', '/api/project/' . $project->getId());
    }

    /**
     * @Route("/project/{id}", requirements={"_method":"GET"})
     */
    public function getProjectAction($id)
    {
        $this->ensureLoggedIn();

        $dm = $this->get('doctrine.odm.mongodb.document_manager');
        $project = $dm->getRepository('RetextApiBundle:Project')
            ->findOneBy(array('owner.$id' => new \MongoId($this->getUser()->getId()), 'id' => $id));

        if (!$project) throw $this->createNotFoundException();
        return $this->createResponse($project);
    }


    /**
     * @Route("/project", requirements={"_method":"GET"})
     */
    public function listProjectAction()
    {
        $this->ensureLoggedIn();

        $dm = $this->get('doctrine.odm.mongodb.document_manager');
        $projects = $dm->getRepository('RetextApiBundle:Project')
            ->createQueryBuilder()
            ->hydrate(false)
            ->field('owner.$id')->equals(new \MongoId($this->getUser()->getId()))
            ->select('_id', 'name')
            ->getQuery()
            ->execute();

        $ps = array();
        foreach($projects as $project) {
            $ps[] = array('id' => (string)$project['_id'], 'name' => $project['name']);
        }
        return $this->createListResponse($ps);
    }
}
