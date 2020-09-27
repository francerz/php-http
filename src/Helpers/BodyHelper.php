<?php

namespace Francerz\Http\Helpers;

use Francerz\Http\BodyParsers;
use Francerz\Http\StringStream;
use Psr\Http\Message\MessageInterface;

class BodyHelper
{
    public static function getParsedBody(MessageInterface $message)
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
    public static function withContent(MessageInterface $message, string $mediaType, $content)
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