<?php

namespace Francerz\Http;

use Francerz\Http\Base\ServerRequestBase;
use Francerz\Http\Helpers\BodyHelper;
use Francerz\Http\Traits\MessageTrait;

class ServerRequest extends ServerRequestBase
{
    use MessageTrait;

    protected $parsedBody;
    
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
        if (isset($this->parsedBody)) {
            return $this->parsedBody;
        }

        return $this->parsedBody = BodyHelper::getParsedBody($this);
    }
    public function withParsedBody($data)
    {
        $new = clone $this;

        $new->parsedBody = $data;

        $contentType = $this->getContentType();
        if (empty($contentType)) {
            $this->body = new StringStream((string)$this->parsedBody);
            return $new;
        }

        $parser = BodyParsers::find($contentType);
        if (empty($parser)) {
            $this->body = new StringStream((string)$this->parsedBody);
            return $new;
        }

        $this->body = $parser->encode($data);
        return $new;
    }
}