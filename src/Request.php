<?php
namespace Francerz\Http;

use Francerz\Http\Base\RequestBase;
use Francerz\Http\Constants\Methods;
use Francerz\Http\Traits\MessageTrait;

class Request extends RequestBase
{
    use MessageTrait;
    
    public function __construct(Uri $uri, string $method = Methods::GET)
    {
        parent::__construct();
        $this->method = $method;
        $this->uri = $uri;
        $this->body = new StringStream();
    }
}