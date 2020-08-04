<?php
namespace Francerz\Http;

class Request
{
	const METHOD_UNKNOWN= 0;
	const METHOD_GET	= 'GET';
	const METHOD_POST	= 'POST';
	const METHOD_PUT	= 'PUT';
    const METHOD_DELETE = 'DELETE';

    private $method;
    private $url;
    private $headers;
    private $body;

    private $params = array();
    private $case_headers = array();
    static public function post($url)
    {
        $request = new static($url);
        $request->setMethod(static::METHOD_POST);
        return $request;
    }
    static public function getCurrent()
    {
        $request = new static(Url::getCurrent());
        $request->putHeaders(getallheaders());
        $request->body = @file_get_contents('php://input');
        $request->method = $_SERVER['REQUEST_METHOD'];
        return $request;
    }

    public function __construct($url)
    {
        $this->method = "GET";
        $this->headers = array();
        $this->body = "";
        if (is_string($url)) {
            $this->url = new Url($url);
        } elseif ($url instanceof Url) {
            $this->url = $url;
        } else {
            trigger_error("Unknown URL format");
        }
    }
    public function setMethod($method)
    {
        $this->method = $method;
    }
    public function setUrl(Url $url)
    {
        $this->url = $url;
    }
    public function getUrl()
    {
        return $this->url;
    }
    public function putHeader($name, $value)
    {
        $this->case_headers[strtolower($name)] = $name;
        $this->headers[$name] = $value;
    }
    public function putHeaders($headers)
    {
        foreach ($headers as $name => $content) {
            $this->putHeader($name, $content);
        }
    }
    public function getHeader($name)
    {
        if (!array_key_exists($name, $this->headers)) {
            return null;
        }
        $name = $this->case_headers[strtolower($name)];
        return $this->headers[$name];
    }
    public function putParam($name, $value = null)
    {
        $this->params[$name] = $value;
    }
    public function send()
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        if (!empty($this->timeout)) {
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->timeout);
        }
        if (!empty($this->port)) {
            curl_setopt($ch, CURLOPT_PORT, $this->port);
        }
        if (!empty($this->headers)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, array_map(
                function($v, $k) {
                    return "{$k}: {$v}";
                }, $this->headers, array_keys($this->headers)));
        }
        if (!empty($this->params)) {
            $this->body = http_build_query($this->params);
            curl_setopt_array($ch, array(
                CURLOPT_POST    => count($this->params),
                CURLOPT_POSTFIELDS =>  $this->body
            ));
        }
        $url = $this->url;
        curl_setopt($ch, CURLOPT_URL, $url);

        switch ($this->method) {
            case self::METHOD_GET:
            case self::METHOD_POST:
            case self::METHOD_PUT:
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $this->method);
                break;
            default:
                trigger_error('Unknown Request Method');
                break;
        }
        if (!empty($this->userpwd)) {
            curl_setopt($ch, CURLOPT_USERPWD, $this->userpwd);
        }
        $response = curl_exec($ch);

        if (curl_errno($ch) !== 0) {
            $curl_error = curl_error($ch);
            trigger_error("ERROR: Request->send();\r\n{$curl_error}");
        }

        $httpResponse = Response::fromCURL($ch, $response);

        curl_close($ch);

        return $httpResponse;
    }
}