<?php

namespace Retext\ApiBundle;

use Retext\ApiBundle\Exception\ApiClientException;

/**
 * Ein einfacher HTTP-Client zum Kommunizieren mit der API
 */
class ApiClient
{
    private $apiHost;
    private $cookies = array();
    private $response;

    public function GET($path)
    {
        return $this->request('GET', $path);
    }

    public function POST($path, array $data)
    {
        return $this->request('POST', $path, $data);
    }

    public function PUT($path, array $data)
    {
        return $this->request('PUT', $path, $data);
    }

    protected function request($method, $path, $data = null)
    {
        // Create a stream
        $opts = array(
            'http' => array(
                'method' => $method,
                'header' => "Accept: application/json\r\n",
                'ignore_errors' => true
            )
        );
        foreach ($this->cookies as $k => $v) {
            $opts['http']['header'] .= "Cookie: $k=$v\r\n";
        }
        if ($data !== null) {
            $opts['http']['header'] .= "Content-Type: application/json\r\n";
            $opts['http']['content'] = json_encode($data);
        }
        $context = stream_context_create($opts);
        $this->response = file_get_contents($this->getApiHost() . $path, false, $context);
        $status = (int)substr($http_response_header[0], 9, 3); // e.g. HTTP/1.0 400 Bad Request
        if ($status >= 400) {
            throw new ApiClientException('Request failed: ' . $http_response_header[0] . ' (' . substr($this->response, 0, 255) . ')', $status);
        }
        foreach ($http_response_header as $header) {
            if (preg_match('/^Set-Cookie: ([^=]+)=([^;]+);/', $header, $cookieMatch)) {
                $this->cookies[$cookieMatch[1]] = $cookieMatch[2];
            }
        }
        $data = json_decode($this->response);
        return $data;
    }

    public function getRelationHref(\stdClass $object, $context, $list = false, $role = null)
    {
        if ($object === null || !property_exists($object, '@relations')) throw new \Exception('No @relations in ' . var_export($object, true));
        foreach ($object->{'@relations'} as $relation) {
            if ($relation->relatedcontext != $context) continue;
            if ($relation->list !== $list) continue;
            if ($role !== null && property_exists($relation, 'role') && $role !== $relation->role) continue;
            return $relation->href;
        }
        throw new \Exception('Could not find relation ' . $context . ' (list=' . var_export($list, true) . ', role=' . var_export($role, true) . ') in ' . var_export($object, true));
    }

    public function setApiHost($apiHost)
    {
        if (substr($apiHost, -1) == '/') $apiHost = substr($apiHost, 0, -1);
        $this->apiHost = $apiHost;
    }

    public function getApiHost()
    {
        return $this->apiHost;
    }

    /**
     * @return string
     */
    public function getResponse()
    {
        return $this->response;
    }
}