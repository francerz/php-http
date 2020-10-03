<?php

namespace Francerz\Http\Helpers;

use Psr\Http\Message\UriInterface;

class UriHelper
{
    public static function withQueryParam(UriInterface $uri, string $key, $value) : UriInterface
    {
        parse_str($uri->getQuery(), $params);
        $params[$key] = $value;
        return $uri->withQuery(http_build_query($params));
    }

    public static function withQueryParams(UriInterface $uri, array $params, bool $replace = false) : UriInterface
    {
        if (!$replace) {
            parse_str($uri->getQuery(), $queryParams);
            $params = array_merge($queryParams, $params);
        }
        return $uri->withQuery(http_build_query($params));
    }

    public static function withoutQueryParam(UriInterface $uri, string $name)
    {
        parse_str($uri->getQuery(), $queryParams);
        unset($queryParams[$name]);

        return $uri->withQuery(http_build_query($queryParams));
    }

    public static function getQueryParams(UriInterface $uri) : array
    {
        parse_str($uri->getQuery(), $queryParams);
        return $queryParams;
    }

    public static function getQueryParam(UriInterface $uri, string $name, $default = null)
    {
        parse_str($uri->getQuery(), $queryParams);

        if (array_key_exists($name, $queryParams)) {
            return $queryParams[$name];
        }
        return $default;
    }

    public static function appendPath(UriInterface $uri, string $path)
    {
        // normalizes slash at begin of string
        $path = ($path[0] !== '/' ? '/'.$path : $path);

        $prepath = $uri->getPath();
        if (is_null($prepath)) {
            $prepath = '';
        }
        $prepath = (substr($prepath, -1) === '/' ? substr($prepath, 0, -1) : $prepath);

        return $uri->withPath($prepath.$path);
    }
}