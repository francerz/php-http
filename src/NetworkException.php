<?php

namespace Francerz\Http;

use Exception;
use Psr\Http\Client\NetworkExceptionInterface;
use Psr\Http\Message\RequestInterface;

class NetworkException extends Exception implements NetworkExceptionInterface
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