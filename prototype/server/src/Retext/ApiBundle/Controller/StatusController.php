<?php

namespace Retext\ApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * Controller für den Status
 *
 * @author Markus Tacker <m@tckr.cc>
 */
class StatusController extends Base
{
    /**
     * @Route("/status", requirements={"_method":"GET"})
     */
    public function statusAction()
    {
        return $this->createResponse(array('time' => new \DateTime(), 'version' => 1));
    }
}
