<?php

namespace Retext\ApiBundle\Controller;

use Retext\ApiBundle\ApiResponse;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
Symfony\Component\HttpFoundation\Response,
Symfony\Component\HttpKernel\Exception\HttpException;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

abstract class Base extends Controller
{
    /**
     * @param mixed|null $data
     * @return \Retext\ApiBundle\ApiResponse
     */
    public function createResponse($data = null)
    {
        $response = new ApiResponse();
        $response->addHeader('Content-Type', 'application/json')
            ->addHeader('Access-Control-Allow-Origin', $this->getRequest()->headers->get('Origin'))
            ->addHeader('Access-Control-Allow-Credentials', 'true')
            ->addHeader('Access-Control-Max-Age', '604800')
            ->addHeader('Access-Control-Allow-Methods', 'GET,POST,PUT,DELETE,OPTIONS')
            ->addHeader('Access-Control-Allow-Headers', 'X-CSRF-Token, X-Requested-With, Accept, Accept-Version, Content-Length, Content-MD5, Content-Type, Date, X-Api-Version');
        if ($data !== null) $response->setContent($this->container->get('serializer')->serialize($data, 'json'));
        return $response;
    }

    /**
     * @param mixed|null $data
     * @return \Retext\ApiBundle\ApiResponse
     */
    public function createListResponse($data = null)
    {
        $items = array();
        if ($data instanceof \Iterator) {
            foreach ($data as $d) $items[] = $d;
        }
        return $this->createResponse($items);
    }

    /**
     * @return \Symfony\Component\HttpKernel\Exception\HttpException
     */
    public function createForbiddenException()
    {
        return new HttpException(403, 'Forbidden', null, array('Content-Type' => 'application/json', 'Access-Control-Allow-Origin' => '*'));
    }

    /**
     * Make sure the current user is logged in
     */
    public function ensureLoggedIn()
    {
        if (!$this->getRequest()->getSession()->has('User')) throw $this->createForbiddenException();
    }

    /**
     * @return \Retext\ApiBundle\Document\User|null
     */
    public function getUser()
    {
        $this->ensureLoggedIn();
        return $this->getRequest()->getSession()->get('User');
    }
}
