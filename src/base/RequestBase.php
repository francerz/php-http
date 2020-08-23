<?php

namespace Francerz\Http\Base;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;

abstract class RequestBase extends MessageBase implements RequestInterface
{
    protected $requestTarget;
    protected $method;
    protected $uri;

    public function __construct()
    {
        parent::__construct();
    }

    public function getRequestTarget()
    {
        if (isset($this->requestTarget)) {
            return $this->requestTarget;
        }
        if (isset($this->uri)) {
            $path = $this->uri->getPath();
            $query = $this->uri->getQuery();
            if (empty($query)) {
                return $path;
            }
            return $path.'?'.$query;
        }
        return "/";
    }

    public function withRequestTarget($requestTarget)
    {
        $new = clone $this;
        $new->requestTarget = $requestTarget;
        return $new;
    }

    public function getMethod()
    {
        return $this->method;
    }

    public function withMethod($method)
    {
        $new = clone $this;
        $new->method = $method;
        return $new;
    }

    public function getUri()
    {
        return $this->uri;
    }

    public function withUri(UriInterface $uri, $preserveHost = false)
    {
        $new = clone $this;
        $new->uri = $uri;
        return $new;
    }
}