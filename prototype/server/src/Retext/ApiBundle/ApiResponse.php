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
}
