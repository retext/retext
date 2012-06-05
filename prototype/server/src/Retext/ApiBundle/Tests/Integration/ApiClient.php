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

    public function doRequest($method, $url, array $data = null, $expectedStatus = null)
    {
        if ($expectedStatus === null) $expectedStatus = 200;
        $header = array('HTTP_ACCEPT' => 'application/json');
        if ($method !== 'GET') $header['HTTP_CONTENT_TYPE'] = 'application/json';
        $this->client->request($method, $url, array(), array(), $header, json_encode($data));
        $responseBody = $this->client->getResponse()->getContent();
        if ($expectedStatus !== $this->client->getResponse()->getStatusCode()) throw new \Exception(sprintf("Expected status %d, got %d.\n%s %s\n> %s\n< %s", $expectedStatus, $this->client->getResponse()->getStatusCode(), $method, $url, json_encode($data), $responseBody));

        if (empty($responseBody)) return null;
        $result = json_decode($responseBody);
        if ($expectedStatus === 201) {
            foreach (array('@context', '@subject') as $prop) {
                if (!property_exists($result, $prop)) throw new \Exception(sprintf('Missing attribute %s', $prop));
                if ($result->$prop == null) throw new \Exception(sprintf('Attribute %s must not be null', $prop));
            }
            $location = $this->client->getResponse()->getHeader('Location');
            if (empty($location)) throw new \Exception('Response must contain a Location header');
            if ($location !== $result->{'@subject'}) throw new \Exception('Location header must equal @subject: ' . $location);
        }
        return $result;
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
}
