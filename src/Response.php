<?php
namespace Francerz\Http;

class Response
{
    private $code;
    private $headers;
    private $body;

    private $header_keys = array();

    private function setStatusCode($code)
    {
        $this->code = $code;
    }
    public function getStatusCode()
    {
        return $this->code;
    }
    private function setHeaders($headers)
    {
        foreach ($headers as $name => $content) {
            $this->setHeader($name, $content);
        }
    }
    private function setHeader($name, $content)
    {
        $this->header_keys[strtolower($name)] = $name;
        $this->headers[$name] = $content;
    }
    private function getHeader($name)
    {
        $name = strtolower($name);
        if (empty($this->header_keys[$name])) {
            return null;
        }
        $name = $this->header_keys[$name];
        return $this->headers[$name];
    }
    private function setBody($body)
    {
        $this->body = $body;
    }
    public function isSuccess()
    {
        return $this->code >= 200 && $this->code < 300;
    }
    public function getContentType()
    {
        $ct = $this->getHeader('Content-Type');
        return $ct ?? 'application/octet';
    }
    public function getParsedBody()
    {
        $type = $this->getContentType();
        $lim = strpos($type, ';');
        $type = $lim === false ? $type : substr($type, 0, $lim);
        $parsed_body = $this->body;
        if (strtolower($type) === 'application/json') {
            $parsed_body = json_decode($parsed_body);
        }
        return $parsed_body;
    }
    public function getBody()
    {
        return $this->body;
    }
    public static function fromCURL($curl, $response_body)
    {
        $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        $header_size  = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
        $header_space = trim(substr($response_body, 0, $header_size));
        $content      = substr($response_body, $header_size);

        $headers = explode("\r\n", $header_space);
        $headers_len = count($headers);
        $header_list = array();
        for ($i = 2; $i < $headers_len; $i++) {
            list($header, $h_content) = explode(':', $headers[$i]);
            $header_list[$header] = trim($h_content);
        }

        $response = new static();
        $response->setStatusCode($statusCode);
        $response->setHeaders($header_list);
        $response->setBody($content);

        return $response;
    }
}