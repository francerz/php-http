<?php
namespace Francerz\Http;

use Francerz\Http\Base\RequestBase;
use Francerz\Http\Traits\MessageTrait;
use Francerz\Http\Utils\Constants\Methods;

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