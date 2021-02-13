<?php

namespace Francerz\Http;

use Fig\Http\Message\RequestMethodInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class Client implements ClientInterface
{
    private $userAgent = 'francerz-php-http';
    private $timeout = 30;

    public function setUserAgent($userAgent)
    {
        $this->userAgent = $userAgent;
    }
    public function getUserAgent()
    {
        return $this->userAgent;
    }

    public function setTimeout(int $timeout)
    {
        $this->timeout = $timeout;
    }

    public function getTimeout() : int
    {
        return $this->timeout;
    }

    public function sendRequest(RequestInterface $request) : ResponseInterface
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        
        curl_setopt($ch, CURLOPT_URL, (string)$request->getUri());

        if (!empty($this->timeout)) {
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->timeout);
        }

        $headers = $request->getHeaders();
        if (!empty($headers)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, array_map(
                function($v, $k) {
                    return sprintf('%s: %s', $k, join(',', $v));
                },
                $headers,
                array_keys($headers)
            ));
        }

        $method = $request->getMethod();
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    
        switch ($method) {
            case RequestMethodInterface::METHOD_POST:
            case RequestMethodInterface::METHOD_PUT:
            case RequestMethodInterface::METHOD_PATCH:
                $hasBody = true;
                break;
            case RequestMethodInterface::METHOD_DELETE:
            case RequestMethodInterface::METHOD_GET:
            case RequestMethodInterface::METHOD_OPTIONS:
            case RequestMethodInterface::METHOD_HEAD:
            default:
                $hasBody = false;
                break;
        }

        if ($hasBody) {
            $body = $request->getBody();
            if (!empty($body)) {
                curl_setopt_array($ch, array(
                    CURLOPT_POST => true,
                    CURLOPT_POSTFIELDS => (string)$body
                ));
            }

        }
        
        $response = curl_exec($ch);

        if (curl_errno($ch) !== 0) {
            $curl_error = curl_error($ch);
            throw new RequestException(__CLASS__.'->'.__METHOD__.': '.$curl_error, 0, null, $request);
        }

        $httpResponse = Response::fromCURL($ch, $response);

        curl_close($ch);

        return $httpResponse;
    }
}