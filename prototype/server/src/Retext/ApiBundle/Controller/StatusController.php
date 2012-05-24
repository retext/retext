<?php

namespace Retext\ApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class StatusController extends Base
{
    /**
     * @Route("/status")
     */
    public function statusAction()
    {
        return $this->createResponse(array('time' => new \DateTime(), 'version' => 1));
    }
}
