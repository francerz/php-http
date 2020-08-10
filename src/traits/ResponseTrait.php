<?php

namespace Francerz\Http\Traits;

trait ResponseTrait
{
    public function isSuccess()
    {
        $code = $this->getStatusCode();
        return $code >= 200 && $code < 300;
    }
}