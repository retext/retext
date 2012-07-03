<?php

namespace Retext\ApiBundle\Controller;

use Retext\ApiBundle\Document\User;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
Symfony\Component\HttpFoundation\Response, Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * Controller für die Benutzer-Registrierung
 *
 * @author Markus Tacker <m@tckr.cc>
 */
class RegisterController extends Base
{
    /**
     * @Route("/user", requirements={"_method":"POST"})
     */
    public function registerAction()
    {
        $email = $this->getFromRequest('email');
        $user = new User();
        $user->setEmail($email);
        // TODO: generate Passwords
        $user->setPassword($email);

        $dm = $this->get('doctrine.odm.mongodb.document_manager');
        $dm->persist($user);
        $dm->flush();

        return $this->createResponse()->addHeader('Location', $user->getSubject())->setStatusCode(201);
    }

    /**
     * @Route("/user/{id}", requirements={"_method":"GET"})
     */
    public function getUserAction($id)
    {
        return $this->createResponse($this->getDocument('User', $id));
    }
}
