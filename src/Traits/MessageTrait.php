<?php

namespace Francerz\Http\Traits;

use Francerz\Http\Utils\Headers\AbstractAuthorizationHeader;
use Francerz\Http\Utils\MessageHelper;

trait MessageTrait
{
    public function getContentType()
    {
        $ct = $this->getHeader('Content-Type');
        if (count($ct) == 0) {
            return null;
        }
        $ct = $ct[0];
        return $ct ?? 'application/octet';
    }

    public function withAuthorizationHeader(AbstractAuthorizationHeader $authHeader)
    {
        return $this->withHeader('Authorization', $authHeader);
    }

    public function getAuthorizationHeader() : ?AbstractAuthorizationHeader
    {
        return MessageHelper::getFirstAuthorizationHeader($this);
    }
}