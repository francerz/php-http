<?php

namespace Francerz\Http;

use Francerz\Http\Base\ServerRequestBase;
use Francerz\Http\Tools\MessageHelper;
use Francerz\Http\Traits\MessageTrait;
use Psr\Http\Message\StreamInterface;

class ServerRequest extends ServerRequestBase
{
    use MessageTrait;

    protected $parsedBody;
    
    public function __construct()
    {
        parent::__construct();
    }

    public function getCurrent()
    {
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

    public function withBody(StreamInterface $body)
    {
        $new = parent::withBody($body);
        $new->parsedBody = MessageHelper::getContent($this);
        return $new;
    }

    public function getParsedBody()
    {
        if (isset($this->parsedBody)) {
            return $this->parsedBody;
        }
        return null;
    }
    public function withParsedBody($data)
    {
        $contentType = $this->getContentType();
        $new = MessageHelper::withContent(
            $this,
            $contentType,
            $data
        );
        $new->parsedBody = $data;
        return $new;
    }
}