<?php

namespace Francerz\Http;

use Psr\Http\Message\RequestInterface;

class Client
{
    private $userAgent = 'francerz-http-client-php';
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

    public function send(RequestInterface $request) : Response
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
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
            case Methods::POST:
            case Methods::PUT:
            case Methods::PATCH:
                $hasBody = true;
                break;
            case Methods::DELETE:
            case Methods::GET:
            case Methods::OPTIONS:
            case Methods::HEAD:
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
            throw new \Exception(__CLASS__.'->'.__METHOD__.': '.$curl_error);
        }

        $httpResponse = Response::fromCURL($ch, $response);

        curl_close($ch);

        return $httpResponse;
    }
}