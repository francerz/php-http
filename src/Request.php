<?php
namespace Francerz\Http;

use Francerz\Http\Base\RequestBase;

class Request extends RequestBase
{
    public function __construct(Uri $uri)
    {
        $this->uri = $uri;
    }
}