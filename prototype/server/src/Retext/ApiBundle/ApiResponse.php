<?php

namespace Retext\ApiBundle;

use Symfony\Component\HttpFoundation\Response;

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
