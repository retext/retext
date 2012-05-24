<?php

namespace Retext\ApiBundle\Controller;

use Retext\ApiBundle\ApiResponse;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
Symfony\Component\HttpFoundation\Response;

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
            ->addHeader('Access-Control-Allow-Origin', '*');
        if ($data !== null) $response->setContent($this->container->get('serializer')->serialize($data, 'json'));
        return $response;
    }
}
