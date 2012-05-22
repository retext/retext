<?php

namespace Retext\ApiBundle\Controller;

use Retext\ApiBundle\Document\User;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
Symfony\Component\HttpFoundation\Response, Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class RegisterController extends Controller
{
    /**
     * @Route("/user", requirements={"_method":"PUT"})
     */
    public function registerAction(Request $request)
    {
        $email = $request->get('email');
        $user = new User();
        $user->setEmail($email);
        $user->setPassword($email);

        $dm = $this->get('doctrine.odm.mongodb.document_manager');
        $dm->persist($user);
        $dm->flush();

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->headers->set('Location', '/api/user/' . $user->getId());
        $response->setStatusCode(201);

        return $response;
    }

    /**
     * @Route("/login", requirements={"_method":"POST"})
     */
    public function loginAction(Request $request)
    {
        $email = $request->get('email');
        $password = $request->get('password');

        $dm = $this->get('doctrine.odm.mongodb.document_manager');
        $user = $dm->getRepository('RetextApiBundle:User')
            ->findOneByEmail($email);

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->headers->set('Access-Control-Allow-Origin', '*');

        if ($user && $user->getPassword() === $user->hashPassword($password, $user->getPassword())) {
            $response->setStatusCode(204);
        } else {
            $response->setStatusCode(403);
        }
        return $response;
    }
}
