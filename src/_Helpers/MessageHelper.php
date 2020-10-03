<?php

namespace Francerz\Http\Helpers;

use Francerz\Http\BodyParsers;
use Francerz\Http\StringStream;
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

    public static function getContent(MessageInterface $message)
    {
        $body = $message->getBody();
        $type = $message->getHeader('Content-Type');

        if (empty($type)) {
            return (string)$body;
        }

        $parser = BodyParsers::find($type[0]);
        if (empty($parser)) {
            return (string)$body;
        }

        return $parser->decode($body, $type[0]);
    }

    public static function withContent(MessageInterface $message, string $mediaType, $content) : MessageInterface
    {
        $parser = BodyParsers::find($mediaType);
        
        if (isset($parser)) {
            $body = $parser->encode($content);
        } else {
            $body = new StringStream((string)$content);
        }

        return $message
            ->withBody($body)
            ->withHeader('Content-Type', $mediaType);
    }
}