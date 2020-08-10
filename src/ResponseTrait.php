<?php

namespace Francerz\Http;

trait ResponseTrait
{
    public function isSuccess()
    {
        $code = $this->getStatusCode();
        return $code >= 200 && $code < 300;
    }
    public function getContentType()
    {
        $ct = $this->getHeader('Content-Type');
        if (count($ct) == 0) {
            return null;
        }
        $ct = $ct[0];
        return $ct ?? 'application/octet';
    }
}