<?php

namespace Retext\ApiBundle\Listener;

use Retext\ApiBundle\Controller\ApiResponse;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent,
Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException,
Symfony\Component\HttpFoundation\Response;
use JMS\SerializerBundle\Serializer\Serializer;

/**
 * Diese Klasse lauscht auf Exceptions, die innerhalb von Symfony2 als Events propagiert werden und erstellen eine JSON-Fehlermeldung daraus.
 *
 * @author Markus Tacker <m@tckr.cc>
 */
class Exception
{
    /**
     * @var \JMS\SerializerBundle\Serializer\Serializer
     */
    private $serializer;

    public function __construct(Serializer $serializer)
    {
        $this->serializer = $serializer;
    }

    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();

        // Allow OPTIONS-Requests
        if ($exception instanceof MethodNotAllowedHttpException && $event->getRequest()->getMethod() === 'OPTIONS') {
            $event->setResponse(new Response());
            return;
        }

        $response = new ApiResponse();
        $response->addHeader('Content-Type', 'application/json');

        $data = array(
            'error' => true,
            'code' => $exception->getCode(),
            'message' => $exception->getMessage(),
            'type' => get_class($exception),
        );

        if ($exception instanceof \Symfony\Component\HttpKernel\Exception\HttpException) {
            $response->setStatusCode($exception->getStatusCode());
        } elseif ($exception instanceof \MongoCursorException && $exception->getCode() == 11000) {
            $response->setStatusCode(409);
        } elseif ($exception instanceof \MongoConnectionException) {
            $response->setStatusCode(500);
            $data['code'] = \Retext\ApiBundle\Exception\Base::CODE_DATABASE_ERROR;
        } else {
            $response->setStatusCode(500);
        }
        $response->setContent($this->serializer->serialize($data, 'json'));
        $event->setResponse($response);
    }
}