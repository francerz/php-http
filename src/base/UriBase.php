<?php

namespace Francerz\Http\Base;

use Francerz\Http\Ports;
use Psr\Http\Message\UriInterface;

abstract class UriBase implements UriInterface
{
    protected $scheme;
    protected $user;
    protected $password;
    protected $host;
    protected $port;
    protected $path;
    protected $query;
    protected $fragment;

    public function __construct()
    {
        
    }

    public function getScheme() : string
    {
        if (!isset($this->scheme)) {
            return '';
        }
        return $this->scheme;
    }

    public function getAuthority() : string
    {
        if (!isset($this->host)) {
            return '';
        }

        $authority = $this->getHost();

        $userInfo = $this->getUserInfo();
        if (!empty($userInfo)) {
            $authority = $userInfo.'@'.$authority;
        }

        $port = $this->getPort();
        if (!empty($port)) {
            $authority .= ':'.$port;
        }

        return $authority;
    }

    public function getUserInfo() : string
    {
        if (empty($this->user)) {
            return '';
        }

        $userInfo = $this->user;
        if (!empty($this->password)) {
            $userInfo.= ':'.$this->password;
        }

        return $userInfo;
    }

    public function getHost() : string
    {
        if (!isset($this->host)) {
            return '';
        }
        return $this->host;
    }

    public function getPort() : ?int
    {
        if (
            isset($this->port) &&
            !in_array($this->port, Ports::forScheme($this->scheme), true)
        ) {
            return $this->port;
        }
        return null;
    }

    public function getPath() : string
    {
        if (!isset($this->path)) {
            return '';
        }
        return $this->path;
    }

    public function getQuery() : string
    {
        if (!isset($this->query)) {
            return '';
        }
        return $this->query;
    }

    public function getFragment() : string
    {
        if (!isset($this->fragment)) {
            return '';
        }
        return $this->fragment;
    }

    public function withScheme($scheme) : UriBase
    {
        $new = clone $this;

        $new->scheme = strtolower($scheme);

        return $new;
    }

    public function withUserInfo($user, $password = null) : UriBase
    {
        $new = clone $this;

        $new->user = $user;
        $new->password = $password;

        return $new;
    }

    public function withHost($host) : UriBase
    {
        $new = clone $this;

        $new->host = strtolower($host);

        return $new;
    }

    public function withPort($port) : UriBase
    {
        $new = clone $this;

        $new->port = $port;

        return $new;
    }

    public function withPath($path) : UriBase
    {
        $new = clone $this;

        $new->path = $path;

        return $new;
    }

    public function withQuery($query) : UriBase
    {
        $new = clone $this;

        $new->query = $query;

        return $new;
    }

    public function withFragment($fragment) : UriBase
    {
        $new = clone $this;

        $new->fragment = $fragment;

        return $new;
    }

    public function __toString() : string
    {
        $uri = '';

        $scheme = $this->getScheme();
        if (!empty($scheme)) {
            $uri.= $scheme.':';
        }

        $authority = $this->getAuthority();
        $path = $this->getPath();
        if (!empty($authority)) {
            $uri.= '//'.$authority;

            // Adding "/" at start if path is rootless.
            if (!empty($path) && strpos($path, '/') !== 0) {
                $path = '/'.$path;
            }
            $uri.= $path;
        } elseif (!empty($path)) {
            // Collapses all starting "/" to one.
            $uri.= '/'.ltrim($path, '/');
        }

        $query = $this->getQuery();
        if (!empty($query)) {
            $uri.= '?'.$query;
        }

        $fragment = $this->getFragment();
        if (!empty($fragment)) {
            $uri.= '#'.$fragment;
        }

        return $uri;
    }
}