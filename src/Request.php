<?php
namespace Francerz\Http;

use Francerz\Http\Base\RequestBase;
use Francerz\Http\Traits\MessageTrait;

class Request extends RequestBase
{
    use MessageTrait;
    
    public function __construct(Uri $uri)
    {
        parent::__construct();
        $this->uri = $uri;
        $this->body = new StringStream();
    }
}