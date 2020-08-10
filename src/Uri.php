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
}