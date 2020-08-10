<?php

namespace Francerz\Http;

use Francerz\Http\Base\ServerRequestBase;

class ServerRequest extends ServerRequestBase
{
    use ResponseTrait;
    
    public function __construct()
    {
        parent::__construct();

        $this->initMessageAttributes();
        $this->initRequestAttributes();

        $this->cookies = $_COOKIE;
        $this->params = $_GET;
        $this->files = $_FILES;
    }

    protected function initMessageAttributes()
    {
        $sp = $_SERVER['SERVER_PROTOCOL'];
        $this->protocolVersion = substr($sp, strpos($sp, '/') + 1);

        $headers = getallheaders();
        foreach ($headers as $hname => $hcontent) {
            $this->headers[$hname] = preg_split('/(,\\s*)/', $hcontent);
        }

        $this->body = new StringStream(file_get_contents('php://input'));
    }
    protected function initRequestAttributes()
    {
        $this->uri = Uri::getCurrent();
        $this->method = $_SERVER['REQUEST_METHOD'];
    }

    public function getParsedBody()
    {
        $contentType = $this->getHeader('Content-Type');
        if (count($contentType) === 0) {
            return (string)$this->body;
        }

        $parser = BodyParsers::find($contentType[0]);
        if (is_null($parser)) {
            return (string)$this->body;
        }

        return $parser->decode($this->body, $contentType[0]);
    }
    public function withParsedBody($data)
    {
        
    }
}