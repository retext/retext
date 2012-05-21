<?php

namespace Retext\ApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class StatusController extends Controller
{
    /**
     * @Route("/status")
     */
    public function statusAction()
    {
        $response = new Response($this->container->get('serializer')->serialize(array('time' => new \DateTime(), 'version' => 1), 'json'));
        $response->headers->set('Content-Type', 'application/json');
        $response->headers->set('Access-Control-Allow-Origin', '*');
        return $response;
    }
}
