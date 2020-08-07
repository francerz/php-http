<?php

namespace Francerz\Http\Base;

use Francerz\PowerData\Arrays;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\StreamInterface;

abstract class MessageBase implements MessageInterface
{
    const PROTOCOL_VERSION_1_0 = "1.0";
    const PROTOCOL_VERSION_1_1 = "1.1";

    protected string $protocolVersion = self::PROTOCOL_VERSION_1_1;
    protected array $headers = array();
    protected StreamInterface $body;

    public function __construct()
    {
        
    }

    public function getProtocolVersion()
    {
        return $this->protocolVersion;
    }
    
    public function withProtocolVersion($version)
    {
        $new = clone $this;
        $new->protocolVersion = $version;
        return $new;
    }
    
    protected function getHeaderKey($name)
    {
        return Arrays::keyInsensitive($this->headers, $name);
    }

    public function getHeaders() : array
    {
        return $this->headers;
    }

    public function hasHeader($name) : bool
    {
        $key = $this->getHeaderKey($name);
        return ($key !== null);
    }

    public function getHeader($name) : array
    {
        $key = $this->getHeaderKey($name);
        return $this->headers[$key];
    }

    public function getHeaderLine($name) : string
    {
        $key = $this->getHeaderKey($name);
        return join(',', $this->headers[$key]);
    }

    public function withHeader($name, $value) : MessageBase
    {
        if (!is_array($value)) {
            $value = [$value];
        }
        
        $new = clone $this;

        $key = $new->getHeaderKey($name);
        unset($new->headers[$key]);

        $new->headers[$name] = $value;

        return $new;
    }

    public function withAddedHeader($name, $value) : MessageBase
    {
        $oldValues = $this->getHeader($name);

        if (!is_array($value)) {
            $value = [$value];
        }

        return $this->withHeader($name, array_merge($oldValues, $value));
    }

    public function withoutHeader($name) : MessageBase
    {
        $new = clone $this;

        $key = $new->getHeaderKey($name);
        unset($new->headers[$key]);

        return $new;
    }

    public function getBody() : StreamInterface
    {
        return $this->body;
    }

    public function withBody(StreamInterface $body)
    {
        $new = clone $this;

        return $new;
    }
}