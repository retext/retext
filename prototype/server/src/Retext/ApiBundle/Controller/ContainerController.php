<?php

namespace Retext\ApiBundle\Controller;

use Retext\ApiBundle\RequestParamater, Retext\ApiBundle\Document\Project, Retext\ApiBundle\Document\Container, Retext\ApiBundle\Model\TreeNode;

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

        $container = new Container();
        $container->setProject($project);
        $container->setParent($parent);
        $container->setName($this->getFromRequest(RequestParamater::create('name')->makeOptional()->defaultsTo(null)));

        $dm->persist($container);
        $dm->flush();

        $this->addedChildElement($container);

        return $this->createResponse($container)->setStatusCode(201)->addHeader('Location', $container->getSubject());
    }

    /**
     * Gibt das Projekt und den Eltern-Container zurück.
     *
     * @return array
     */
    protected function getProjectAndParent()
    {
        $parent = $this->getContainer($this->getFromRequest(RequestParamater::create('parent')));
        $project = $parent->getProject();
        return array($parent, $project);
    }

    /**
     * Gibt eine Liste mit den Containern auf einer Ebene eines Projektes zurück
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
            ->field('parent')->equals(new \MongoId($parent->getId()))
            ->field('deletedAt')->exists(false)
            ->getQuery();
        $container = $query->execute();

        return $this->createListResponse($container, $parent->getChildOrder());
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
        $container->setChildOrder($this->getFromRequest(RequestParamater::create('childOrder')->makeOptional()->makeList()->defaultsTo($container->getChildOrder())));

        $dm->persist($container);
        $dm->flush();

        return $this->createResponse($container);
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
        if ($container->isRootContainer()) throw $this->createForbiddenException('Cannot delete root container.');

        // TODO: Check delete permissions
        $sdm = $this->get('doctrine.odm.mongodb.soft_delete.manager');
        $sdm->delete($container);
        $sdm->flush();

        $this->removedChildElement($container);

        return $this->createResponse();
    }

    /**
     * @Route("/container/{container_id}/breadcrumb", requirements={"_method":"GET"})
     */
    public function getContainerBreadcrumbAction($container_id)
    {
        $this->ensureLoggedIn();
        $breadcrumb = array();
        $container = $this->getContainer($container_id);
        if ($container->isRootContainer()) return $this->createResponse();
        $breadcrumb[] = $container;
        while ($container = $container->getParent()) {
            if ($container->isRootContainer()) break;
            array_unshift($breadcrumb, $container);
        }
        return $this->createResponse($breadcrumb);
    }

    /**
     * Gibt das Projekt als Baumstruktur zurück
     *
     * @Route("/container/{container_id}/tree", requirements={"_method":"GET"})
     */
    public function getContainerTreeAction($container_id)
    {
        $this->ensureLoggedIn();

        $container = $this->getContainer($container_id);
        $exportContainerChildren = $this->get('retext.apibundle.export.containerchildren');

        $walkTree = function(Container $parent) use($exportContainerChildren, &$walkTree)
        {
            $elements = array();
            foreach ($exportContainerChildren->getChildren($parent) as $childElement) {
                $node = new TreeNode($childElement);
                $elements[] = $node;
                if ($childElement instanceof Container) $node->children = $walkTree($childElement);
            }
            return $elements;
        };
        $tree = $walkTree($container);
        return $this->createResponse($tree);
    }

}
