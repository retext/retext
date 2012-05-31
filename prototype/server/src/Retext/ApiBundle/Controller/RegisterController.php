<?php

namespace Retext\ApiBundle\Controller;

use Retext\ApiBundle\Document\User;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
Symfony\Component\HttpFoundation\Response, Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class RegisterController extends Base
{
    /**
     * @Route("/user", requirements={"_method":"POST"})
     */
    public function registerAction(Request $request)
    {
        $email = $this->getFromRequest('email');
        $user = new User();
        $user->setEmail($email);
        // TODO: generate Passwords
        $user->setPassword($email);

        $dm = $this->get('doctrine.odm.mongodb.document_manager');
        $dm->persist($user);
        $dm->flush();

        return $this->createResponse()->setStatusCode(201)->addHeader('Location', $user->getSubject());
    }
}
