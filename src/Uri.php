<?php

namespace Francerz\Http;

use Francerz\Http\Utils\UriHelper;
use Psr\Http\Message\UriInterface;

class Uri implements UriInterface
{
    protected $scheme;
    protected $user;
    protected $password;
    protected $host;
    protected $port;
    protected $path;
    protected $query;
    protected $fragment;

    #region UriInterface
    public function getScheme(): string
    {
        if (!isset($this->scheme)) {
            return '';
        }
        return $this->scheme;
    }

    public function getAuthority(): string
    {
        if (!isset($this->host)) {
            return '';
        }
        $authority = $this->host;
        if (!empty($this->user)) {
            $authority = $this->getUserInfo() . '@' . $authority;
        }
        if (isset($this->port)) {
            $authority .= ':' . $this->port;
        }
        return $authority;
    }

    public function getUserInfo(): string
    {
        if (empty($this->user)) {
            return '';
        }

        $userInfo = $this->user;
        if (!empty($this->password)) {
            $userInfo .= ':' . $this->password;
        }

        return $userInfo;
    }

    public function getHost(): string
    {
        if (!isset($this->host)) {
            return '';
        }
        return $this->host;
    }

    public function getPort(): ?int
    {
        if (
            isset($this->port) &&
            !in_array($this->port, Ports::forScheme($this->scheme), true)
        ) {
            return $this->port;
        }
        return null;
    }

    public function getPath(): string
    {
        if (!isset($this->path)) {
            return '';
        }
        return $this->path;
    }

    public function getQuery(): string
    {
        if (!isset($this->query)) {
            return '';
        }
        return $this->query;
    }

    public function getFragment(): string
    {
        if (!isset($this->fragment)) {
            return '';
        }
        return $this->fragment;
    }

    public function withScheme($scheme): UriInterface
    {
        $new = clone $this;
        $new->scheme = strtolower($scheme);
        return $new;
    }

    public function withUserInfo($user, $password = null): UriInterface
    {
        $new = clone $this;
        $new->user = $user;
        $new->password = $password;
        return $new;
    }

    public function withHost($host): UriInterface
    {
        $new = clone $this;
        $new->host = strtolower($host);
        return $new;
    }

    public function withPort($port): UriInterface
    {
        $new = clone $this;
        $new->port = $port;
        return $new;
    }

    public function withPath($path): UriInterface
    {
        $new = clone $this;
        $new->path = $path;
        return $new;
    }

    public function withQuery($query): UriInterface
    {
        $new = clone $this;
        $new->query = $query;
        return $new;
    }

    public function withFragment($fragment): UriInterface
    {
        $new = clone $this;
        $new->fragment = $fragment;
        return $new;
    }

    public function __toString(): string
    {
        return UriHelper::buildStringFromParts([
            'scheme' => $this->scheme,
            'user' => $this->user,
            'pass' => $this->password,
            'host' => $this->host,
            'port' => $this->port,
            'path' => $this->path,
            'query' => $this->query,
            'fragment' => $this->fragment
        ]);
    }
    #endregion

    /**
     * @deprecated v0.3.0
     *
     * @return Uri
     */
    public static function getCurrent(): UriInterface
    {
        return new static(UriHelper::getCurrentString());
    }

    public function __construct($uri = null)
    {
        if (is_string($uri)) {
            $this->parse($uri);
        } elseif ($uri instanceof UriInterface) {
            $this->loadFromUriInterface($uri);
        }
    }

    private function parse($url)
    {
        $uriParts = parse_url($url);
        $this->scheme = $uriParts['scheme'] ?? null;
        $this->user = $uriParts['user'] ?? '';
        $this->password = $uriParts['pass'] ?? null;
        $this->host = $uriParts['host'] ?? '';
        $this->port = $uriParts['port'] ?? null;
        $this->path = $uriParts['path'] ?? '/';
        $this->query = $uriParts['query'] ?? '';
        $this->fragment = $uriParts['fragment'] ?? '';
    }

    private function loadFromUriInterface(UriInterface $uri)
    {
        $this->scheme = $uri->getScheme();
        $this->user = UriHelper::getUser($uri);
        $this->password = UriHelper::getPassword($uri);
        $this->host = $uri->getHost();
        $this->port = $uri->getPort();
        $this->path = $uri->getPath();
        $this->query = $uri->getQuery();
        $this->fragment = $uri->getFragment();
    }

    /**
     * @deprecated v0.3.0
     *
     * @return void
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @deprecated v0.3.0
     *
     * @return void
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @deprecated v0.3.0
     *
     * @param string $name
     * @param [type] $value
     * @return Uri
     */
    public function withQueryParam(string $name, $value): Uri
    {
        return UriHelper::withQueryParam($this, $name, $value);
    }

    /**
     * @deprecated v0.3.0
     *
     * @param array $params
     * @param boolean $replace
     * @return Uri
     */
    public function withQueryParams(array $params, bool $replace = false): Uri
    {
        return UriHelper::withQueryParams($this, $params, $replace);
    }

    /**
     * @deprecated v0.3.0
     *
     * @param string $name
     * @return Uri
     */
    public function withoutQueryParam(string $name): Uri
    {
        return UriHelper::withoutQueryParam($this, $name);
    }

    /**
     * @deprecated v0.3.0
     *
     * @param string $name
     * @return void
     */
    public function getQueryParam(string $name)
    {
        return UriHelper::getQueryParam($this, $name);
    }

    /**
     * @deprecated v0.3.0
     *
     * @return void
     */
    public function getQueryParams()
    {
        return UriHelper::getQueryParams($this);
    }
}
