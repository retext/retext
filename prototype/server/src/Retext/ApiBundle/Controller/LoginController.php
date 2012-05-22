<?php

namespace Retext\ApiBundle\Controller;

use Retext\ApiBundle\Document\User;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
Symfony\Component\HttpFoundation\Response, Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class LoginController extends Controller
{
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
            session_start();
            $_SESSION['User'] = $user;
            $response->setStatusCode(204);
        } else {
            $response->setStatusCode(403);
        }
        return $response;
    }
}
