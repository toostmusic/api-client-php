<?php

namespace NilandApi;

use GuzzleHttp\Client as httpClient;
use GuzzleHttp\Exception\TransferException;
use GuzzleHttp\Exception\ClientException;
use NilandApi\Exceptions\NotFoundException;
use NilandApi\Exceptions\AuthenticationFailedException;
use NilandApi\Exceptions\BadRequestException;
use NilandApi\Exceptions\ForbiddenException;
use NilandApi\Exceptions\RuntimeException;
use NilandApi\Exceptions\ConflictException;

class Client
{
    private $httpClient;

    public function __construct($apiKey, $apiBaseUrl = 'https://api.niland.io', $apiVersion = '2.0')
    {
        $apiBaseUrl = rtrim($apiBaseUrl, '/');

        $this->httpClient = new HttpClient(array(
            'base_uri' => sprintf('%s/%s/', $apiBaseUrl, $apiVersion),
            'query'    => array('key' => $apiKey)
        ));
    }

    public function get($path, array $params = array())
    {
        foreach ($params as &$value) {
            if (is_array($value)) {
                $value = implode(',', $value);
            }
        }

        $params = array_merge(
            $this->httpClient->getConfig('query'),
            $params
        );

        return $this->request('GET', $path, array('query' => $params));
    }

    public function post($path, array $data, $multipart = false)
    {
        $data = $this->prepareData($data);

        return $this->request('POST', $path, $data);
    }

    public function patch($path, array $data)
    {
        $data = $this->prepareData($data);

        return $this->request('PATCH', $path, $data);
    }

    public function delete($path)
    {
        return $this->request('DELETE', $path);
    }

    private function request($method, $path, array $options = array())
    {
        $path = ltrim($path, '/');
        $path = rtrim($path, '/');

        try {
            return json_decode(
                $this->httpClient->request($method, $path, $options)->getBody()->getContents(),
                true
            );
        } catch (TransferException $e) {
            if ($e instanceof ClientException) {
                switch ($e->getResponse()->getStatusCode()) {
                    case 400:
                        throw new BadRequestException(
                            $e->getResponse()->getBody()->getContents()
                        );
                    case 401:
                        throw new AuthenticationFailedException();
                    case 403:
                        throw new ForbiddenException();
                    case 404:
                        throw new NotFoundException(
                            $e->getRequest()->getUri()->__toString()
                        );
                    case 409:
                        throw new ConflictException();
                }
            }
            throw new RuntimeException($e->getMessage());
        }

    }

    private function prepareData(array $data)
    {
        $body      = array();
        $multipart = false;

        foreach ($data as $element) {
            if (is_resource($element)) {
                $multipart = true;
            }
        }

        if ($multipart) {
            foreach ($data as $field => $value) {
                if (is_resource($value)) {
                    $body['multipart'][] = array(
                        'name'     => $field,
                        'contents' => $value
                    );
                } elseif (is_array($value)) {
                    foreach ($value as $key => $element) {
                        $body['multipart'][] = array(
                            'name'     => sprintf('%s[%s]', $field, $key),
                            'contents' => (string) $element
                        );
                    }
                } else {
                    $body['multipart'][] = array(
                        'name'     => $field,
                        'contents' => (string) $value
                    );
                }
            }
        } else {
            $body['form_params'] = $data;
        }

        return $body;
    }
}
