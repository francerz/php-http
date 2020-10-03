<?php

namespace Francerz\Http\Base;

use Francerz\PowerData\Arrays;
use Francerz\PowerData\Type;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UploadedFileInterface;

abstract class ServerRequestBase extends RequestBase implements ServerRequestInterface
{
    protected $cookies;
    protected $params;
    protected $files;
    protected $attributes;

    public function getServerParams() : array
    {
        return $_SERVER;
    }

    public function getCookieParams() : array
    {
        return $this->cookies;
    }

    public function withCookieParams(array $cookies) : ServerRequestBase
    {
        if (Arrays::hasNumericKeys($cookies)) {
            throw new \InvalidArgumentException('$cookies argument must be key/value pairs array.');
        }
        $new = clone $this;
        $new->cookies = $cookies;
        return $new;
    }

    public function getQueryParams() : array
    {
        return $this->params;
    }

    public function withQueryParams(array $query) : ServerRequestBase
    {
        $new = clone $this;
        $new->params = $query;
        return $new;
    }

    public function getUploadedFiles() : array
    {
        return $this->files;
    }

    public function withUploadedFiles(array $uploadedFiles) : ServerRequestBase
    {
        $uploadedFileArrayType = Type::for(UploadedFileInterface::class, 1);
        if (!$uploadedFileArrayType->check($uploadedFiles)) {
            throw new \InvalidArgumentException('$uploadedFiles argument must be a UploadedFileInterface array.');
        }

        $new = clone $this;
        $new->files = $uploadedFiles;
        return $new;
    }

    public abstract function getParsedBody();
    
    public abstract function withParsedBody($data);

    public function getAttributes() : array
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

    public function withAttribute($name, $value) : ServerRequestBase
    {
        $new = clone $this;

        $new->attributes[$name] = $value;

        return $new;
    }

    public function withoutAttribute($name) : ServerRequestBase
    {
        $new = clone $this;

        if (array_key_exists($name, $new->attributes)) {
            unset($new->attributes[$name]);
        }

        return $new;
    }
}