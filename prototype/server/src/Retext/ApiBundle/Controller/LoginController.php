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
        list($email, $password) = $this->getFromRequest('email', 'password');
        $dm = $this->get('doctrine.odm.mongodb.document_manager');
        $user = $dm->getRepository('RetextApiBundle:User')
            ->findOneByEmail($this->getFromRequest('email'));

        if ($user && $user->getPassword() === $user->hashPassword($password, $user->getPassword())) {
            $this->getRequest()->getSession()->set('User', $user);
            return $this->createResponse($user);
        }
        throw $this->createForbiddenException();
    }

    /**
     * @Route("/auth", requirements={"_method":"GET"})
     */
    public function authAction()
    {
        if ($this->getRequest()->getSession()->has('User')) {
            return $this->createResponse()->setStatusCode(204);
        }
        throw $this->createForbiddenException();
    }

    /**
     * @Route("/logout", requirements={"_method":"POST"})
     */
    public function logoutAction()
    {
        $this->getRequest()->getSession()->remove('User');
        return $this->createResponse()->setStatusCode(204);
    }
}
