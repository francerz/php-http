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

        $authority = $this->getHost();
        $userInfo = $this->getUserInfo();
        if (!empty($userInfo)) {
            $authority = $userInfo . '@' . $authority;
        }

        $port = $this->getPort();
        if (!empty($port)) {
            $authority .= ':' . $port;
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

    public function withScheme($scheme): Uri
    {
        $new = clone $this;
        $new->scheme = strtolower($scheme);
        return $new;
    }

    public function withUserInfo($user, $password = null): Uri
    {
        $new = clone $this;
        $new->user = $user;
        $new->password = $password;
        return $new;
    }

    public function withHost($host): Uri
    {
        $new = clone $this;
        $new->host = strtolower($host);
        return $new;
    }

    public function withPort($port): Uri
    {
        $new = clone $this;
        $new->port = $port;
        return $new;
    }

    public function withPath($path): Uri
    {
        $new = clone $this;
        $new->path = $path;
        return $new;
    }

    public function withQuery($query): Uri
    {
        $new = clone $this;
        $new->query = $query;
        return $new;
    }

    public function withFragment($fragment): Uri
    {
        $new = clone $this;
        $new->fragment = $fragment;
        return $new;
    }

    public function __toString(): string
    {
        $uri = '';

        $scheme = $this->getScheme();
        if (!empty($scheme)) {
            $uri .= $scheme . ':';
        }

        $authority = $this->getAuthority();
        $path = $this->getPath();
        if (!empty($authority)) {
            $uri .= '//' . $authority;

            // Adding "/" at start if path is rootless.
            if (!empty($path) && strpos($path, '/') !== 0) {
                $path = '/' . $path;
            }
            $uri .= $path;
        } elseif (!empty($path)) {
            // Collapses all starting "/" to one.
            $uri .= '/' . ltrim($path, '/');
        }

        $query = $this->getQuery();
        if (!empty($query)) {
            $uri .= '?' . $query;
        }

        $fragment = $this->getFragment();
        if (!empty($fragment)) {
            $uri .= '#' . $fragment;
        }

        return $uri;
    }
    #endregion

    /**
     * @deprecated v0.3.0
     *
     * @return Uri
     */
    public static function getCurrent(): Uri
    {
        $url = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http';
        $url .= '://';
        $url .= $_SERVER['HTTP_HOST'] ?? 'localhost';
        $url .= $_SERVER['REQUEST_URI'] ?? '/index.php';
        $url  = new static($url);
        return $url;
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
        $p = '`(?:(.*?):)?(?:/{2}(?:([^:@]*)(:[^@]*)?@)?([^:/?#]+)(?::(\\d+))?)?([^?#]*)?([^#]*)?(.*)`';
        preg_match($p, $url, $m);

        $this->scheme = $m[1];
        $this->user = $m[2];
        $this->password = substr($m[3], 1);
        $this->host = $m[4];
        $this->port = intval($m[5]);
        $this->path = $m[6];
        $this->query = substr($m[7], 1);
        $this->fragment = substr($m[8], 1);
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
