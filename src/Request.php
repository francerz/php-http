<?php

namespace Francerz\Http;

use Fig\Http\Message\RequestMethodInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;

class Request extends AbstractMessage implements RequestInterface
{
    protected $requestTarget;
    protected $method;
    protected $uri;

    public function __construct(Uri $uri, string $method = RequestMethodInterface::METHOD_GET)
    {
        parent::__construct();
        $this->method = $method;
        $this->uri = $uri;
        $this->body = new StringStream();
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
            return $path . '?' . $query;
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
