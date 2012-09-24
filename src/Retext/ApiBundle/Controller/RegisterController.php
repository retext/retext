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
        $code = $this->getFromRequest(RequestParameter::create('code')->makeOptional()->regexFormat('^[a-z]{3}-[a-z]{3}-[a-z]{3}$'));

        $user = new User();
        $user->setEmail($email);
        $password = $this->generatePassword();
        $user->setPassword($password);
        $user->setCode($code);

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

    private function generatePassword()
    {
        $pass = '';
        $chars = 'abcdefghkmnpqrstuvwxyzABCDEFGHKMNPQRSTUVWXYZ23456789#+*§$%&-_';
        $clen = strlen($chars) - 1;
        while (strlen($pass) < 12) {
            $pass .= $chars[rand(0, $clen)];
        }
        return $pass;
    }
}
