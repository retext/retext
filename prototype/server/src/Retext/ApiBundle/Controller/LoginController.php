<?php

namespace Retext\ApiBundle\Controller;

use Retext\ApiBundle\Document\User;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
Symfony\Component\HttpFoundation\Response, Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class LoginController extends Base
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

        if ($user && $user->getPassword() === $user->hashPassword($password, $user->getPassword())) {
            $_SESSION['User'] = $user;
            return $this->createResponse($user);
        }
        return $this->createResponse()->setStatusCode(403);
    }

    /**
     * @Route("/auth", requirements={"_method":"GET"})
     */
    public function authAction()
    {
        if (isset($_SESSION['User'])) {
            return $this->createResponse()->setStatusCode(204);
        }
        return $this->createResponse()->setStatusCode(403);
    }

    /**
     * @Route("/logout", requirements={"_method":"POST"})
     */
    public function logoutAction()
    {
        unset($_SESSION['User']);
        return $this->createResponse()->setStatusCode(204);
    }
}
