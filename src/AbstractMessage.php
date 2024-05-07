<?php

namespace Francerz\Http;

use Francerz\Http\Utils\HttpHelper;
use Francerz\PowerData\Arrays;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\StreamInterface;

/**
 * @internal
 */
abstract class AbstractMessage implements MessageInterface
{
    protected $http;

    protected $protocolVersion = '1.1';
    protected $headers = array();
    protected $body;

    public function __construct()
    {
        $this->http = new HttpHelper(HttpFactory::getManager());
    }

    public function getProtocolVersion(): string
    {
        return $this->protocolVersion;
    }

    public function withProtocolVersion(string $version): MessageInterface
    {
        $new = clone $this;
        $new->protocolVersion = $version;
        return $new;
    }

    protected function getHeaderKey($name)
    {
        return Arrays::keyInsensitive($this->headers, $name);
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function hasHeader($name): bool
    {
        $key = $this->getHeaderKey($name);
        return ($key !== null);
    }

    public function getHeader($name): array
    {
        $key = $this->getHeaderKey($name);
        if (is_null($key)) {
            return [];
        }
        return $this->headers[$key];
    }

    public function getHeaderLine($name): string
    {
        $header = $this->getHeader($name);
        return join(', ', $header);
    }

    public function withHeader($name, $value): MessageInterface
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

    public function withAddedHeader($name, $value): MessageInterface
    {
        $oldValues = $this->getHeader($name);

        if (!is_array($value)) {
            $value = [$value];
        }

        return $this->withHeader($name, array_merge($oldValues, $value));
    }

    public function withoutHeader($name): MessageInterface
    {
        $new = clone $this;

        $key = $new->getHeaderKey($name);
        unset($new->headers[$key]);

        return $new;
    }

    public function getBody(): StreamInterface
    {
        if (is_null($this->body)) {
            $this->body = new StringStream();
        }
        return $this->body;
    }

    public function withBody(StreamInterface $body): MessageInterface
    {
        $new = clone $this;
        $new->body = $body;
        return $new;
    }
}
