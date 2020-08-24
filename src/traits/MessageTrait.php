<?php

namespace Francerz\Http\Traits;

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
        $header = $this->getHeader('Authorization');

        $wsp = strpos($header, ' ');
        $type = ucfirst(strtolower(substr($header, 0, $wsp)));
        $content = base64_decode(substr($header, $wsp + 1));

        $result = [];
        if ($type === 'Basic') {
            $a = explode(':', $content);
            $result['user'] = $a[0];
            $result['pass'] = $a[1];
        }

        return $result;
    }
}