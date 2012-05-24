<?php

namespace Retext\ApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class DummyController extends Base
{
    /**
     * @Route("/hello/{name}")
     */
    public function helloAction($name)
    {
        return $this->createResponse(array('name' => $name));
    }
}
