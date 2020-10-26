<?php
namespace Francerz\Http;

use Francerz\Http\Base\UriBase;
use Francerz\Http\Utils\UriHelper;
use Psr\Http\Message\UriInterface;

class Uri extends UriBase
{

    static public function getCurrent() : Uri
    {
        $url = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http';
        $url.= '://';
        $url.= $_SERVER['HTTP_HOST'];
        $url.= $_SERVER['REQUEST_URI'];
        $url = new static($url);
        return $url;
    }
    
    public function __construct($uri = null)
    {
        parent::__construct();
        if (is_string($uri)) {
            $this->parse($uri);
        } elseif ($uri instanceof UriInterface) {
            $this->loadFromUriInterface($uri);
        }
    }

    private function parse($url)
    {
        $p = '`(?:(.*?):)?(?:/{2}(?:([^:@]*)(:[^@]*)?@)?([^:/?#]+)(?::(\\d+))?)?(/[^?#]*)?([^#]*)?(.*)`';
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
        $userInfo = $uri->getUserInfo();
        if (is_string($userInfo)) {
            $lim = strpos($userInfo, ':');
            if ($lim !== false) {
                $this->user = substr($userInfo, $lim);
                $this->password = substr($userInfo, $lim + 1);
            } else {
                $this->user = $userInfo;
            }
        }
        $this->host = $uri->getHost();
        $this->port = $uri->getPort();
        $this->path = $uri->getPath();
        $this->query = $uri->getQuery();
        $this->fragment = $uri->getFragment();
    }

    public function withQueryParam(string $name, $value) : Uri
    {
        return UriHelper::withQueryParam($this, $name, $value);
    }

    public function withQueryParams(array $params, bool $replace = false) : Uri
    {
        return UriHelper::withQueryParams($this, $params, $replace);
    }

    public function withoutQueryParam(string $name) : Uri
    {
        return UriHelper::withoutQueryParam($this, $name);
    }

    public function getQueryParam(string $name)
    {
        return UriHelper::getQueryParam($this, $name);
    }

    public function getQueryParams()
    {
        return UriHelper::getQueryParams($this);
    }
}