<?php

namespace Francerz\Http;

use Fig\Http\Message\RequestMethodInterface;
use Francerz\Http\Utils\Constants\MediaTypes;
use Francerz\Http\Utils\HttpHelper;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

class ServerRequest extends Request implements ServerRequestInterface
{

    protected $parsedBody;
    protected $serverParams;
    protected $cookies;
    protected $query;
    protected $files;
    protected $attributes;

    protected $post;

    public function __construct(
        UriInterface $uri,
        $method = RequestMethodInterface::METHOD_GET,
        array $serverParams = []
    ) {
        parent::__construct($uri, $method);
        $this->serverParams = $serverParams;
        $this->init();
    }

    private function init()
    {
        // protocolVersion
        $sp = $_SERVER['SERVER_PROTOCOL'] ?? 'HTTP/1.0';
        $this->protocolVersion = substr($sp, strpos($sp, '/') + 1);

        // headers
        $headers = getallheaders();
        foreach ($headers as $hname => $hcontent) {
            $this->headers[$hname] = preg_split('/(,\\s*)/', $hcontent);
        }

        $this->files = $this->http->normalizeFiles($_FILES);
        $this->cookies = $_COOKIE;
        $this->query = $_GET;

        // body
        $streamFactory = $this->http->getHttpFactoryManager()->getStreamFactory();
        $this->body = $streamFactory->createStreamFromFile('php://input');

        // parsedBody
        $this->parsedBody = $this->initParsedBody();
    }

    private function initParsedBody()
    {
        if ($this->method === RequestMethodInterface::METHOD_POST) {
            $contentType = $this->getHeaderLine('Content-Type');
            if (
                in_array(
                    $contentType,
                    [MediaTypes::APPLICATION_X_WWW_FORM_URLENCODED, MediaTypes::MULTIPART_FORM_DATA]
                )
            ) {
                return $_POST;
            }
        }
        return HttpHelper::getContent($this);
    }

    public function getServerParams()
    {
        return $this->serverParams;
    }

    public function getCookieParams()
    {
        return $this->cookies;
    }

    public function withCookieParams(array $cookies): ServerRequest
    {
        $new = clone $this;
        $new->cookies = $cookies;
        return $new;
    }

    public function getQueryParams()
    {
        return $this->query;
    }

    public function withQueryParams(array $query): ServerRequest
    {
        $new = clone $this;
        $new->query = $query;
        return $new;
    }

    public function getUploadedFiles()
    {
        return $this->files;
    }

    public function withUploadedFiles(array $uploadedFiles): ServerRequest
    {
        if (!HttpHelper::isUploadedFileArray($uploadedFiles)) {
            throw new \InvalidArgumentException('Argument MUST be a UploadedFileInterface array.');
        }

        $new = clone $this;
        $new->files = $uploadedFiles;
        return $new;
    }

    public function withBody(StreamInterface $body)
    {
        $new = parent::withBody($body);
        $new->parsedBody = null;
        return $new;
    }

    public function getParsedBody()
    {
        if (isset($this->parsedBody)) {
            return $this->parsedBody;
        }
        $this->parsedBody = HttpHelper::getContent($this);
        return $this->parsedBody;
    }
    public function withParsedBody($data)
    {
        $contentType = $this->getHeaderLine('Content-Type');
        $new = $this->http->withContent($this, $contentType, $data);
        $new->parsedBody = $data;
        return $new;
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function getAttribute($name, $default = null)
    {
        if (!array_key_exists($name, $this->attributes)) {
            return $default;
        }
        return $this->attributes[$name];
    }

    public function withAttribute($name, $value): ServerRequest
    {
        $new = clone $this;
        $new->attributes[$name] = $value;
        return $new;
    }

    public function withoutAttribute($name): ServerRequest
    {
        $new = clone $this;
        if (array_key_exists($name, $new->attributes)) {
            unset($new->attributes[$name]);
        }
        return $new;
    }
}
