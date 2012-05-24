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
            $_SESSION['User'] = $user;
            $response->setContent($this->container->get('serializer')->serialize($user, 'json'));
            $response->setStatusCode(200);
        } else {
            $response->setStatusCode(403);
        }
        return $response;
    }

    /**
     * @Route("/auth", requirements={"_method":"GET"})
     */
    public function authAction()
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->headers->set('Access-Control-Allow-Origin', '*');
        if (isset($_SESSION['User'])) {
            $response->setStatusCode(204);
        } else {
            $response->setStatusCode(403);
        }
        return $response;
    }

    /**
     * @Route("/logout", requirements={"_method":"POST"})
     */
    public function logoutAction()
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->headers->set('Access-Control-Allow-Origin', '*');
        unset($_SESSION['User']);
        $response->setStatusCode(204);
        return $response;
    }
}
