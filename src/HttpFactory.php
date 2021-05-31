<?php

namespace Francerz\Http;

use Francerz\Http\Utils\HttpFactoryManager;
use InvalidArgumentException;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UploadedFileFactoryInterface;
use Psr\Http\Message\UploadedFileInterface;
use Psr\Http\Message\UriFactoryInterface;
use Psr\Http\Message\UriInterface;

class HttpFactory implements
    RequestFactoryInterface,
    ResponseFactoryInterface,
    ServerRequestFactoryInterface,
    StreamFactoryInterface,
    UploadedFileFactoryInterface,
    UriFactoryInterface
{
    public static function getManager() : HttpFactoryManager
    {
        return new HttpFactoryManager(new static());
    }
    
    public function createRequest(string $method, $uri): RequestInterface
    {
        if (is_string($uri)) {
            $uri = $this->createUri($uri);
        }
        if (!$uri instanceof Uri) {
            throw new InvalidArgumentException('Invalid uri argument.');
        }
        return new Request($uri, $method);
    }

    public function createResponse(int $code = 200, string $reasonPhrase = ''): ResponseInterface
    {
        $response = new Response();
        $response = $response->withStatus($code, $reasonPhrase);
        return $response;
    }

    public function createServerRequest(string $method, $uri, array $serverParams = []): ServerRequestInterface
    {
        if (is_string($uri)) {
            $uri = new $this->createUri($uri);
        }
        if (!$uri instanceof Uri) {
            throw new InvalidArgumentException('Invalid uri argument.');
        }
        $request = new ServerRequest();
        return $request
            ->withMethod($method)
            ->withUri($uri);
    }

    public function createStream(string $content = ''): StreamInterface
    {
        return new StringStream($content);
    }

    public function createStreamFromFile(string $filename, string $mode = 'r'): StreamInterface
    {
        return new FileStream($filename, $mode);
    }

    public function createStreamFromResource($resource): StreamInterface
    {
        return new ResourceStream($resource);
    }
    public function createUri(string $uri = ''): UriInterface
    {
        return new Uri($uri);
    }

    public function createUploadedFile(
        StreamInterface $stream,
        ?int $size = null,
        int $error = \UPLOAD_ERR_OK,
        ?string $clientFilename = null,
        ?string $clientMediaType = null
    ): UploadedFileInterface {
        return new UploadedFile($stream, $size, $error, $clientFilename, $clientMediaType);
    }
}