<?php

namespace Retext\ApiBundle\Controller;

use Retext\ApiBundle\Controller\RequestParameter, Retext\ApiBundle\Document\Project, Retext\ApiBundle\Model\ProjectProgress, Retext\ApiBundle\Document\Container, \Retext\ApiBundle\Model\ProjectContributor;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
Symfony\Component\HttpFoundation\Response, Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * Controller für die Projekte
 *
 * @author Markus Tacker <m@tckr.cc>
 */
class ProjectController extends Base
{
    /**
     * @Route("/project", requirements={"_method":"POST"})
     */
    public function createProjectAction()
    {
        $this->ensureLoggedIn();

        $defaultLanguageName = 'de';

        $project = new Project();
        $project->setOwner($this->getUser());
        $project->setName($this->getFromRequest('name'));
        $project->setDefaultLanguage($defaultLanguageName);

        $dm = $this->get('doctrine.odm.mongodb.document_manager');
        $dm->persist($project);
        $dm->flush();

        // Root-Container anlegen
        $rootContainer = new Container();
        $rootContainer->setRootContainer(true);
        $rootContainer->setProject($project);
        $project->setRootContainer($rootContainer);
        $dm->persist($rootContainer);

        // Standard-Sprache anlegen
        $defaultLanguage = new \Retext\ApiBundle\Document\Language();
        $defaultLanguage->setName($defaultLanguageName);
        $defaultLanguage->setDescription('Deutsch');
        $defaultLanguage->setProject($project);
        $dm->persist($defaultLanguage);

        $dm->flush();

        return $this->createResponse($project)->setStatusCode(201)->addHeader('Location', $project->getSubject());
    }

    /**
     * @Route("/project/{id}", requirements={"_method":"GET"})
     */
    public function getProjectAction($id)
    {
        $this->ensureLoggedIn();

        return $this->createResponse($this->getProject($id));
    }

    /**
     * @Route("/project", requirements={"_method":"GET"})
     */
    public function listProjectAction()
    {
        $this->ensureLoggedIn();

        $dm = $this->get('doctrine.odm.mongodb.document_manager');
        $projectsByOwner = $dm->getRepository('RetextApiBundle:Project')
            ->createQueryBuilder()
            ->field('owner')->equals(new \MongoId($this->getUser()->getId()))
            ->getQuery()
            ->execute();
        $projectsByContributor = $dm->getRepository('RetextApiBundle:Project')
            ->createQueryBuilder()
            ->field('contributors')->all(array($this->getUser()->getEmail()))
            ->getQuery()
            ->execute();
        $projects = array();
        foreach ($projectsByOwner as $project) $projects[] = $project;
        foreach ($projectsByContributor as $project) $projects[] = $project;
        return $this->createListResponse($projects);
    }


    /**
     * @Route("/project/{id}/progress", requirements={"_method":"GET"})
     */
    public function getProjectProgress($id)
    {
        $this->ensureLoggedIn();

        $project = $this->getProject($id);

        $dm = $this->get('doctrine.odm.mongodb.document_manager');
        $qb = $dm->createQueryBuilder('RetextApiBundle:Text')
            ->field('project')->equals(new \MongoId($project->getId()))
            ->field('deletedAt')->exists(false)
            ->map('function() {
                emit("approved", {yes: this.approved ? 1 : 0, no: !this.approved ? 1 : 0});
                emit("spellingApproved", {yes: this.spellingApproved ? 1 : 0, no: !this.spellingApproved ? 1 : 0});
                emit("contentApproved", {yes: this.contentApproved ? 1 : 0, no: !this.contentApproved ? 1 : 0});
                var allApproved = this.approved && this.spellingApproved && this.contentApproved;
                emit("total", {yes: allApproved ? 1 : 0, no: !allApproved ? 1 : 0});
            }')
            ->reduce('function(k, vals) {
                var sums = {yes: 0, no: 0};
                for (var i in vals) {
                    sums.yes += vals[i].yes;
                    sums.no += vals[i].no;
                }
                return sums;
            }');
        $query = $qb->getQuery();
        $result = $query->execute();
        $data = array(
            'approved' => array('yes' => 0, 'no' => 0, 'progress' => 0),
            'contentApproved' => array('yes' => 0, 'no' => 0, 'progress' => 0),
            'spellingApproved' => array('yes' => 0, 'no' => 0, 'progress' => 0),
            'total' => array('yes' => 0, 'no' => 0, 'progress' => 0),
        );
        foreach ($result as $stats) {
            $data[$stats['_id']] = $stats['value'];
            $total = ($stats['value']['yes'] + $stats['value']['no']);
            $data[$stats['_id']]['progress'] = $total > 0 ? $stats['value']['yes'] / $total : 0;
        }

        $progress = new ProjectProgress();
        $progress->setProject($project);
        $progress->setApproved($data['approved']);
        $progress->setContentApproved($data['contentApproved']);
        $progress->setSpellingApproved($data['spellingApproved']);
        $progress->setTotal($data['total']);
        return $this->createResponse($progress);
    }

    /**
     * Fügt dem Projekt einen Mitarbeiter hinzu
     *
     * @Route("/project/{id}/contributor", requirements={"_method":"POST"})
     */
    public function addContributorAction($id)
    {
        $this->ensureLoggedIn();
        $project = $this->getProject($id);
        $email = $this->getFromRequest(new RequestParameter('email'));
        $project->addContributor($email);
        $dm = $this->get('doctrine.odm.mongodb.document_manager');
        $dm->persist($project);
        $dm->flush();
        $projectContributor = new ProjectContributor();
        $projectContributor->setProject($project);
        $projectContributor->setEmail($email);
        return $this->createResponse($projectContributor);
    }

    /**
     * Gibt die Projektmitarbeiter zurück
     *
     * @Route("/project/{id}/contributor", requirements={"_method":"GET"})
     */
    public function listContributorAction($id)
    {
        $this->ensureLoggedIn();
        $project = $this->getProject($id);
        $contributors = array();
        foreach ($project->getContributors() as $email) {
            $contributor = new ProjectContributor();
            $contributor->setProject($project);
            $contributor->setEmail($email);
            $contributors[] = $contributor;
        }
        return $this->createListResponse($contributors);
    }

    /**
     * Entfernt einen einen Mitarbeiter aus dem Projekt
     *
     * @Route("/project/{id}/contributor/{email}", requirements={"_method":"DELETE"})
     */
    public function removeContributorAction($id, $email)
    {
        $this->ensureLoggedIn();
        $project = $this->getProject($id);
        $project->removeContributor(rawurldecode($email));
        $dm = $this->get('doctrine.odm.mongodb.document_manager');
        $dm->persist($project);
        $dm->flush();
        return $this->createResponse();
    }

    /**
     * Fügt dem Projekt eine Sprache hinzu
     *
     * @Route("/project/{id}/language", requirements={"_method":"POST"})
     */
    public function addLanguageAction($id)
    {
        $this->ensureLoggedIn();
        $project = $this->getProject($id);

        $language = new \Retext\ApiBundle\Document\Language();
        $language->setProject($project);
        $language->setName($this->getFromRequest(new RequestParameter('name')));
        $language->setDescription($this->getFromRequest(RequestParameter::create('description')->makeOptional()));

        $dm = $this->get('doctrine.odm.mongodb.document_manager');
        $dm->persist($language);
        $dm->flush();
        return $this->createResponse($language);
    }

    /**
     * Gibt die Projektsprachen zurück
     *
     * @Route("/project/{id}/language", requirements={"_method":"GET"})
     */
    public function listLanguageAction($id)
    {
        $this->ensureLoggedIn();
        $project = $this->getProject($id);
        $dm = $this->get('doctrine.odm.mongodb.document_manager');
        $languages = $dm->getRepository('RetextApiBundle:Language')
            ->createQueryBuilder()
            ->field('project')->equals(new \MongoId($project->getId()))
            ->field('deletedAt')->exists(false)
            ->getQuery()
            ->execute();
        return $this->createListResponse($languages);
    }

    /**
     * Entfernt einen eine Sprache aus dem Projekt
     *
     * @Route("/project/{id}/language/{language_id}", requirements={"_method":"DELETE"})
     */
    public function removeLanguageAction($id, $language_id)
    {
        $this->ensureLoggedIn();
        $project = $this->getProject($id);
        $language = $this->getDocument('Language', $language_id, function(\Doctrine\ODM\MongoDB\Query\Builder $qb) use($project)
        {
            $qb->field('project')->equals(new \MongoId($project->getId()));
        });

        $sdm = $this->get('doctrine.odm.mongodb.soft_delete.manager');
        $sdm->delete($language);
        $sdm->flush();

        return $this->createResponse();
    }
}
