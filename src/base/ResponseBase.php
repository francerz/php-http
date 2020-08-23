<?php

namespace Francerz\Http\Base;

use Francerz\Http\Base\MessageBase;
use Psr\Http\Message\ResponseInterface;

abstract class ResponseBase extends MessageBase implements ResponseInterface
{
    protected $code;
    protected $reasonPhrase;

    public function getStatusCode() : int
    {
        return $this->code;
    }

    public function withStatus($code, $reasonPhrase = '')
    {
        $new = clone $this;

        $new->code = $code;
        $new->reasonPhrase = $reasonPhrase;

        return $new;
    }

    public function getReasonPhrase()
    {
        return $this->reasonPhrase;
    }
}