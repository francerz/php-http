<?php
namespace Francerz\Http;

class Url
{
    private $protocol = 'http';
    private $prot_sep = '://';
    private $user;
    private $password;
    private $host;
    private $path;
    private $params = array();
    private $fragment;

    public function __construct($url = null)
    {
        if (!empty($url)) {
            $this->parse($url);
        }
    }
    static public function getCurrent()
    {
        $url = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http';
        $url.= '://';
        $url.= $_SERVER['HTTP_HOST'];
        $url.= $_SERVER['REQUEST_URI'];
        $url = new static($url);
        return $url;
    }

    private function parse($url)
    {
        $p = '`(?:(.*?)(:/*))?(?:([^/?#:]*)(:[^@]+)@)?([^/?#]+)?([^?#]*)?([^#]*)?(.*)`';
        preg_match($p, $url, $m);

        $this->protocol = $m[1];
        $this->prot_sep = $m[2];
        $this->user = $m[3];
        $this->password = substr($m[4], 1);
        $this->host = $m[5];
        $this->path = $m[6];
        parse_str(substr($m[7], 1), $this->params);
        $this->fragment = substr($m[8], 1);
    }
    public function setProtocol($protocol)
    {
        $this->protocol = $protocol;
    }
    public function setHost($host)
    {
        $this->host = $host;
    }
    public function setPath($path)
    {
        $this->path = $path;
    }
    public function appendPath($path)
    {
        $path = ltrim($path,'/');
        $this->setPath($this->getPath().'/'.$path);
    }
    public function getPath()
    {
        return $this->path;
    }
    public function putParam($name, $value)
    {
        if (is_object($value) && method_exists($value,'__toString')) {
            $this->params[$name] = $value->__toString();
        } else {
            $this->params[$name] = $value;
        }
    }
    public function putParams($params)
    {
        foreach ($params as $name => $value) {
            $this->putParam($name, $value);
        }
    }
    public function getParams()
    {
        return $this->params;
    }
    public function setFragment($fragment)
    {
        $this->fragment = $fragment;
    }
    public function __toString()
    {
        $url = $this->protocol;
        $url.= $this->prot_sep;
        if (!empty($this->user)) {
            $url.= $this->user;
            if (!empty($this->password)) {
                $url.= ':'.$this->password;
            }
            $url.= '@';
        }
        $url.= $this->host;
        if (!empty($this->port)) {
            $url.= ':'.$this->port;
        }
        if (!empty($this->path)) {
            $url.= '/'.ltrim($this->path,'/');
        }
        if (!empty($this->params)) {
            $url.= '?'.http_build_query($this->params);
        }
        if (!empty($this->fragment)) {
            $url.= '#'.$this->fragment;
        }
        return $url;
    }
}