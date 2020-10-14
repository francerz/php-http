<?php

namespace Francerz\Http;

use Exception;
use Psr\Http\Client\RequestExceptionInterface;
use Psr\Http\Message\RequestInterface;

class RequestException extends Exception implements RequestExceptionInterface
{
    private $request;

    public function __construct($message = '', $code = 0, \Throwable $previous = null, RequestInterface $request)
    {
        parent::__construct($message, $code, $previous);
        $this->request = $request;
    }

    public function getRequest(): RequestInterface
    {
        return $this->request;
    }
}