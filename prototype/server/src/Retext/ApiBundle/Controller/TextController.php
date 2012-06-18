<?php

namespace Retext\ApiBundle\Controller;

use Retext\ApiBundle\Controller\RequestParameter,
Retext\ApiBundle\Document\Project,
Retext\ApiBundle\Document\Text,
Retext\ApiBundle\Document\TextVersion,
Retext\ApiBundle\Document\TextType,
Retext\ApiBundle\Document\Comment;

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

        $parent = $this->getContainer($this->getFromRequest(RequestParameter::create('parent')));
        $project = $parent->getProject();

        $type = $this->getTypeByName($project, $this->getFromRequest(RequestParameter::create('type')->makeOptional()->defaultsTo('default')));
        $text = new Text();
        $text->setProject($project);
        $text->setParent($parent);
        $text->setName($this->getFromRequest(RequestParameter::create('name')->makeOptional()->defaultsTo(null)));
        $textValue = $this->getFromRequest(RequestParameter::create('text')->makeOptional()->defaultsTo(null));
        $text->setText($textValue);
        $text->setType($type);

        $textVersion = new TextVersion();
        $textVersion->setProject($project);
        $textVersion->setParent($text);
        $textVersion->setText($textValue);

        $dm = $this->get('doctrine.odm.mongodb.document_manager');
        $dm->persist($text);
        $dm->flush();
        $dm->persist($textVersion);
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
        $text->setName($this->getFromRequest(RequestParameter::create('name')->makeOptional()->defaultsTo($text->getName())));
        $userComment = $this->getFromRequest(RequestParameter::create('comment')->makeOptional());

        // Freigabe
        $newSpellingApproved = $this->getFromRequest(RequestParameter::create('spellingApproved')->makeOptional()->makeBoolean()->defaultsTo($text->getSpellingApproved()));
        $newContentApproved = $this->getFromRequest(RequestParameter::create('contentApproved')->makeOptional()->makeBoolean()->defaultsTo($text->getContentApproved()));
        $newApproved = $this->getFromRequest(RequestParameter::create('approved')->makeOptional()->makeBoolean()->defaultsTo($text->getApproved()));

        $comment = '';
        if ($newSpellingApproved !== $text->getSpellingApproved()) {
            $comment = 'Rechtschreibung ' . ($newSpellingApproved ? 'akzeptiert' : 'abgelehnt') . '. ';
        }
        if ($newContentApproved !== $text->getContentApproved()) {
            $comment = 'Inhalt ' . ($newContentApproved ? 'akzeptiert' : 'abgelehnt') . '. ';
        }
        if ($newApproved !== $text->getApproved()) {
            $comment = 'Freigabe ' . ($newApproved ? 'erteilt' : 'abgelehnt') . '. ';
        }
        if (!empty($userComment)) $comment .= $userComment;
        if (!empty($comment)) $this->createComment($text, $comment);
        $text->setSpellingApproved($newSpellingApproved);
        $text->setContentApproved($newContentApproved);
        $text->setApproved($newApproved);

        $newText = $this->getFromRequest(RequestParameter::create('text')->makeOptional()->defaultsTo($text->getText()));
        if ($newText != $text->getText()) {
            $textVersion = new TextVersion();
            $textVersion->setProject($text->getProject());
            $textVersion->setParent($text);
            $textVersion->setText($newText);
            $dm->persist($textVersion);
        }
        $text->setText($newText);
        $typeName = $this->getFromRequest(RequestParameter::create('type')->makeOptional()->defaultsTo($text->getTypeName()));
        if ($typeName !== null) $text->setType($this->getTypeByName($text->getProject(), $typeName));

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

        $this->removedChildElement($text);

        return $this->createResponse();
    }

    /**
     * @param \Retext\ApiBundle\Document\Project $project
     * @param $typeName
     * @return \Retext\ApiBundle\Document\TextType $type
     */
    protected function getTypeByName(Project $project, $typeName)
    {
        $dm = $this->get('doctrine.odm.mongodb.document_manager');
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
            $dm->persist($type);
            $dm->flush();
        }
        return $type;
    }

    /**
     * @Route("/text/{text_id}/history", requirements={"_method":"GET"})
     */
    public function getTextHistoryAction($text_id)
    {
        $this->ensureLoggedIn();
        $text = $this->getText($text_id);
        $dm = $this->get('doctrine.odm.mongodb.document_manager');
        $history = $dm->getRepository('RetextApiBundle:TextVersion')
            ->createQueryBuilder()
            ->field('project')->equals(new \MongoId($text->getProject()->getId()))
            ->field('parent')->equals(new \MongoId($text->getId()))
            ->field('deletedAt')->exists(false)
            ->sort('_id', 'desc')
            ->getQuery()
            ->execute();
        return $this->createListResponse($history);
    }

    /**
     * @Route("/text/{text_id}/comments", requirements={"_method":"GET"})
     */
    public function getTextCommentsAction($text_id)
    {
        $this->ensureLoggedIn();
        $text = $this->getText($text_id);
        $dm = $this->get('doctrine.odm.mongodb.document_manager');
        $comments = $dm->getRepository('RetextApiBundle:Comment')
            ->createQueryBuilder()
            ->field('project')->equals(new \MongoId($text->getProject()->getId()))
            ->field('text')->equals(new \MongoId($text->getId()))
            ->field('deletedAt')->exists(false)
            ->sort('_id', 'desc')
            ->getQuery()
            ->execute();
        return $this->createListResponse($comments);
    }

    /**
     * @Route("/text/{text_id}/comments", requirements={"_method":"POST"})
     */
    public function addTextCommentAction($text_id)
    {
        $this->ensureLoggedIn();
        $text = $this->getText($text_id);
        $comment = $this->createComment($text, $this->getFromRequest(RequestParameter::create('comment')));
        return $this->createResponse($comment)->setStatusCode(201)->addHeader('Location', $comment->getSubject());
    }

    /**
     * @param \Retext\ApiBundle\Document\Text $text
     * @param string $comment
     * @return \Retext\ApiBundle\Document\Comment
     */
    protected function createComment(Text $text, $comment)
    {
        $c = new Comment();
        $c->setComment($comment);
        $c->setUser($this->getUser());
        $c->setText($text);
        $c->setProject($text->getProject());
        $dm = $this->get('doctrine.odm.mongodb.document_manager');
        $dm->persist($c);
        $dm->getRepository('RetextApiBundle:Text')
            ->createQueryBuilder()
            ->findAndUpdate()
            ->field('id')->equals(new \MongoId($text->getId()))
            ->update()
            ->field('commentCount')->inc(1)
            ->getQuery()
            ->execute();
        $dm->flush();
        return $c;
    }
}
