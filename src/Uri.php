<?php
namespace Francerz\Http;

use Francerz\Http\Base\UriBase;

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
    
    public function __construct(string $url = null)
    {
        parent::__construct();
        if (!empty($url)) {
            $this->parse($url);
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

    public function withQueryParam(string $name, $value) : Uri
    {
        parse_str($this->getQuery(), $queryParams);
        $queryParams[$name] = $value;

        return parent::withQuery(http_build_query($queryParams));
    }

    public function withQueryParams(array $params, bool $replace = false) : Uri
    {
        if (!$replace) {
            parse_str($this->getQuery(), $queryParams);
            $params = array_merge($queryParams, $params);
        }
        return parent::withQuery(http_build_query($params));
    }

    public function withoutQueryParam(string $name) : Uri
    {
        parse_str($this->getQuery(), $queryParams);
        unset($queryParams[$name]);

        return parent::withQuery(http_build_query($queryParams));
    }

    public function getQueryParam(string $name)
    {
        static $queryParams;
        parse_str($this->getQuery(), $queryParams);

        if (array_key_exists($name, $queryParams)) {
            return $queryParams[$name];
        }

        return null;
    }

    public function getQueryParams()
    {
        static $queryParams;
        parse_str($this->getQuery(), $queryParams);

        return $queryParams;
    }
}