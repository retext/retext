<?php

namespace Retext\ApiBundle\Controller;

use Retext\ApiBundle\ApiResponse,
Retext\ApiBundle\RequestParamater,
Retext\ApiBundle\Document\LinkedData;

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
        $response->addHeader('Content-Type', 'application/json');
        /* CORS stuff
            ->addHeader('Access-Control-Allow-Origin', $this->getRequest()->headers->get('Origin'))
            ->addHeader('Access-Control-Allow-Credentials', 'true')
            ->addHeader('Access-Control-Max-Age', '604800')
            ->addHeader('Access-Control-Allow-Methods', 'GET,POST,PUT,DELETE,OPTIONS')
            ->addHeader('Access-Control-Allow-Headers', 'X-CSRF-Token, X-Requested-With, Accept, Accept-Version, Content-Length, Content-MD5, Content-Type, Date, X-Api-Version');
        */
        if ($data !== null) {
            $response->setContent($this->container->get('serializer')->serialize($data, 'json'));
        }
        return $response;
    }

    /**
     * @param mixed|null $data
     * @return \Retext\ApiBundle\ApiResponse
     */
    public function createListResponse($data = null)
    {
        $items = array();
        if ($data instanceof \Iterator || is_array($data)) {
            foreach ($data as $d) $items[] = $d;
        }
        return $this->createResponse($items);
    }

    /**
     * @return \Symfony\Component\HttpKernel\Exception\HttpException
     */
    public function createException($code, $message)
    {
        return new HttpException($code, $message, null, array('Content-Type' => 'application/json'));
    }

    /**
     * @return \Symfony\Component\HttpKernel\Exception\HttpException
     */
    public function createForbiddenException()
    {
        return $this->createException(403, 'Forbidden');
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

    public function ensureRequest()
    {
        $request = $this->getRequest();
        if (!in_array('application/json', $request->getAcceptableContentTypes())) throw $this->createException(406, 'Not Acceptable | You must accept application/json');
        if ((int)$request->headers->get('Content-Length') > 0) if ($request->getContentType() != 'json') throw $this->createException(400, 'Bad Request | Content-Type must be application/json');
    }

    /**
     * @param $key
     * @return array|string
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     */
    public function getFromRequest()
    {
        $this->ensureRequest();
        $request = $this->getRequest();
        $data = json_decode($request->getContent());
        if ($data === null) $data = new \stdClass();
        $args = func_get_args();
        $getKey = function($key) use($data)
        {
            if (!($key instanceof RequestParamater)) {
                /** @var \Retext\ApiBundle\RequestParamater $key  */
                $key = RequestParamater::create($key);
            }
            if (!property_exists($data, $key->getName())) {
                if ($key->isRequired()) {
                    throw $this->createException(400, 'Bad Request | Missing input: ' . $key);
                }
                return $key->getDefaultValue();
            }
            switch ($key->getFormat()) {
                case RequestParamater::FORMAT_INTEGER;
                    return (int)$data->{$key->getName()};
                default:
                    return $data->{$key->getName()};
            }
        };
        if (count($args) == 1) {
            return $getKey($args[0]);
        } else {
            $return = array();
            foreach ($args as $key) $return[] = $getKey($key);
            return $return;
        }
    }
}
