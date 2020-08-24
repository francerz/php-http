<?php

namespace Francerz\Http\Traits;

use Francerz\Http\Helpers\MessageHelper;
use Psr\Http\Message\MessageInterface;

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

    public function withAuthorizationHeader(string $type, string $content) : MessageInterface
    {
        if (strtolower($type) === 'basic') {
            $content = base64_encode($content);
        }
        return $this->withHeader('Authorization', "$type $content");
    }

    public function getAuthorizationHeader(string &$type, string &$content)
    {
        return MessageHelper::getAuthorizationHeader($this, $type, $content);
    }
}