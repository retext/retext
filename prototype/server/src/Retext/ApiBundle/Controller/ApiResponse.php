<?php

namespace Retext\ApiBundle\Controller;

use Symfony\Component\HttpFoundation\Response;

/**
 * Wrapper für die Symfony2-Response-Klasse mit einigen Zusatzfunktionen.
 *
 * @see \Symfony\Component\HttpFoundation\Response
 * @author Markus Tacker <m@tckr.cc>
 */
class ApiResponse extends Response
{
    public function addHeader($name, $value)
    {
        $this->headers->set($name, $value);
        return $this;
    }

    public function getHeader($key, $first = true)
    {
        return $this->headers->get($key, null, $first);
    }

    public function getHeaderCookies()
    {
        return $this->headers->getCookies();
    }
}
