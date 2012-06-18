<?php

namespace Retext\ApiBundle\Tests\Integration;

use Symfony\Bundle\FrameworkBundle\Client;

class ApiClient
{
    protected $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function doRequest($method, $url, array $data = null, $expectedStatus = null, $accept = null)
    {
        if ($expectedStatus === null) $expectedStatus = 200;
        if ($accept === null) $accept = 'application/json';
        $header = array('HTTP_ACCEPT' => $accept);
        if ($method !== 'GET') $header['HTTP_CONTENT_TYPE'] = 'application/json';
        $this->client->request($method, $url, array(), array(), $header, json_encode($data));
        $responseBody = $this->getResponse()->getContent();
        if ($expectedStatus !== $this->getResponse()->getStatusCode()) throw new \Exception(sprintf("Expected status %d, got %d.\n%s %s\n> %s\n< %s", $expectedStatus, $this->getResponse()->getStatusCode(), $method, $url, json_encode($data), $responseBody));

        if (empty($responseBody)) return null;
        if (stristr($this->getResponse()->headers->get('Content-Type'), $accept) === false) throw new \Exception(sprintf("Expected response to be %s, got %s instead", $accept, $this->getResponse()->headers->get('Content-Type')));
        if ($accept == 'application/json') {
            $result = json_decode($responseBody);
            if ($expectedStatus === 201) {
                foreach (array('@context', '@subject') as $prop) {
                    if (!property_exists($result, $prop)) throw new \Exception(sprintf('Missing attribute %s', $prop));
                    if ($result->$prop == null) throw new \Exception(sprintf('Attribute %s must not be null', $prop));
                }
                $location = $this->getResponse()->headers->get('Location');
                if (empty($location)) throw new \Exception('Response must contain a Location header');
                if ($location !== $result->{'@subject'}) throw new \Exception('Location header must equal @subject: ' . $location);
            }
            return $result;
        } else {
            return $responseBody;
        }
    }

    public function GET($url)
    {
        return $this->doRequest('GET', $url, null, 200);
    }

    public function POST($url, array $data = null)
    {
        return $this->doRequest('POST', $url, $data, 200);
    }

    public function UPDATE($url, array $data = null)
    {
        return $this->doRequest('PUT', $url, $data, 200);
    }

    public function CREATE($url, array $data = null)
    {
        return $this->doRequest('POST', $url, $data, 201);
    }

    public function DELETE($url)
    {
        return $this->doRequest('DELETE', $url);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getResponse()
    {
        return $this->client->getResponse();
    }
}
