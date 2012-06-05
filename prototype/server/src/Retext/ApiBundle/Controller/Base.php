<?php

namespace Retext\ApiBundle\Controller;

use Retext\ApiBundle\ApiResponse,
Retext\ApiBundle\RequestParamater,
Retext\ApiBundle\Document\LinkedData,
Retext\ApiBundle\Document\Element;

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
     * @param mixed|null $order list of ids in the order the items should appear in the list
     * @return \Retext\ApiBundle\ApiResponse
     */
    public function createListResponse($data = null, $order = null)
    {
        if ($data instanceof \Iterator || is_array($data)) {
            $items = array();
            $itemPos = array();
            $orderItems = $order !== null;
            if ($orderItems) $idPos = array_flip($order);
            foreach ($data as $d) {
                $items[] = $d;
                if ($orderItems && $d instanceof \Retext\ApiBundle\Document\Element) $itemPos[] = $idPos[$d->getId()];
            }
            if ($orderItems) array_multisort($itemPos, SORT_ASC, $items);
        } else {
            $items = array($data);
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
    public function createForbiddenException($message = null)
    {
        return $this->createException(403, $message == null ? 'Forbidden' : $message);
    }


    /**
     * @return \Symfony\Component\HttpKernel\Exception\HttpException
     */
    public function createGoneException($message = null)
    {
        return $this->createException(410, $message == null ? 'Gone' : $message);
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
        //$this->ensureRequest();
        $request = $this->getRequest();

        if ($request->getMethod() === 'GET') {
            $hasKey = function(RequestParamater $key) use($request)
            {
                return $request->query->has($key->getName());
            };
            $getKeyValue = function(RequestParamater $key) use($request)
            {
                return $request->query->get($key->getName());
            };
        } else {
            $data = json_decode($request->getContent());
            if ($data === null) $data = new \stdClass();
            $hasKey = function(RequestParamater $key) use($data)
            {
                return property_exists($data, $key->getName());
            };
            $getKeyValue = function(RequestParamater $key) use($data)
            {
                return $data->{$key->getName()};
            };
        }

        $getKey = function($key) use($hasKey, $getKeyValue)
        {
            if (!($key instanceof RequestParamater)) {
                /** @var \Retext\ApiBundle\RequestParamater $key  */
                $key = RequestParamater::create($key);
            }
            $value = $getKeyValue($key);
            if (!$hasKey($key) || empty($value)) {
                if ($key->isRequired()) {
                    throw $this->createException(400, 'Bad Request | Missing input: ' . $key->getName());
                }
                return $key->getDefaultValue();
            }
            switch ($key->getFormat()) {
                case RequestParamater::FORMAT_INTEGER;
                    return (int)$value;
                case RequestParamater::FORMAT_LIST:
                    $data = $value;
                    if (!is_array($data)) throw $this->createException(400, 'Bad Request | input ' . $key->getName() . ' must be list');
                    return $data;
                default:
                    return $value;
            }
        };

        $args = func_get_args();
        if (count($args) == 1) {
            return $getKey($args[0]);
        } else {
            $return = array();
            foreach ($args as $key) $return[] = $getKey($key);
            return $return;
        }

    }

    /**
     * @param string $project_id
     * @return \Retext\ApiBundle\Document\Project
     */
    protected function getProject($project_id)
    {
        $dm = $this->get('doctrine.odm.mongodb.document_manager');
        $project = $dm->getRepository('RetextApiBundle:Project')
            ->createQueryBuilder()
            ->field('id')->equals(new \MongoId($project_id))
            ->field('owner')->equals(new \MongoId($this->getUser()->getId()))
            ->getQuery()
            ->getSingleResult();
        if ($project === null)
            throw $this->createNotFoundException('Project ' . $project_id . ' not found.');
        if ($project->getDeletedAt() !== null)
            throw $this->createGoneException('Project ' . $project_id . ' has been deleted.');
        return $project;
    }

    /**
     * @param string $container_id
     * @return \Retext\ApiBundle\Document\Container
     */
    protected function getContainer($container_id)
    {
        $dm = $this->get('doctrine.odm.mongodb.document_manager');
        $container = $dm->getRepository('RetextApiBundle:Container')
            ->createQueryBuilder()
            ->field('id')->equals(new \MongoId($container_id))
            ->getQuery()
            ->getSingleResult();

        if ($container === null)
            throw $this->createNotFoundException('Container ' . $container_id . ' not found.');
        if ($container->getDeletedAt() !== null)
            throw $this->createGoneException('Container ' . $container_id . ' has been deleted.');
        return $container;
    }

    /**
     * @param string $text_id
     * @return \Retext\ApiBundle\Document\Text
     */
    protected function getText($text_id)
    {
        $dm = $this->get('doctrine.odm.mongodb.document_manager');
        $text = $dm->getRepository('RetextApiBundle:Text')
            ->createQueryBuilder()
            ->field('id')->equals(new \MongoId($text_id))
            ->getQuery()
            ->getSingleResult();

        if ($text === null)
            throw $this->createNotFoundException('Text ' . $text_id . ' not found.');
        if ($text->getDeletedAt() !== null)
            throw $this->createGoneException('Text ' . $text_id . ' has been deleted.');
        return $text;
    }

    /**
     * Aktualisiert das Eltern-Element von $element, wenn dieses gelöscht wird
     *
     * @param $element
     */
    protected function removedChildElement(Element $element)
    {
        $dm = $this->get('doctrine.odm.mongodb.document_manager');
        $dm->getRepository('RetextApiBundle:Container')
            ->createQueryBuilder()
            ->findAndUpdate()
            ->field('id')->equals(new \MongoId($element->getParent()->getId()))
            ->update()
            ->field('childCount')->inc(-1)
            ->field('childOrder')->pull($element->getId())
            ->getQuery()
            ->execute();
    }

    /**
     * Aktualisiert das Eltern-Element von $element, wenn dieses hinzugefügt wird
     *
     * @param $element
     */
    protected function addedChildElement(Element $element)
    {
        $dm = $this->get('doctrine.odm.mongodb.document_manager');
        $dm->getRepository('RetextApiBundle:Container')
            ->createQueryBuilder()
            ->findAndUpdate()
            ->field('id')->equals(new \MongoId($element->getParent()->getId()))
            ->update()
            ->field('childCount')->inc(1)
            ->field('childOrder')->push($element->getId())
            ->getQuery()
            ->execute();
    }


}
