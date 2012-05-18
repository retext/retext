<?php

namespace Retext\ApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class DummyController extends Controller
{
    /**
     * @Route("/hello/{name}")
     */
    public function helloAction($name)
    {
        $response = new Response($this->container->get('serializer')->serialize(array('name' => $name), 'json'));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }
}
