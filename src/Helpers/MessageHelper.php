<?php

namespace Francerz\Http\Helpers;

use Psr\Http\Message\MessageInterface;

class MessageHelper
{
    public static function getAuthorizationHeader(MessageInterface $message, &$type, &$content)
    {
        $header = $message->getHeader('Authorization');
        $result = [];

        if (count($header) == 0) {
            return $result;
        }
        $header = $header[0];

        $wsp = strpos($header, ' ');
        $type = ucfirst(strtolower(substr($header, 0, $wsp)));
        $content = substr($header, $wsp + 1);

        if ($type === 'Basic') {
            $a = explode(':', base64_decode($content));
            $result['user'] = $a[0];
            $result['password'] = $a[1];
        }

        return $result;
    }
}