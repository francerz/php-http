<?php

namespace Francerz\Http\Helpers;

use Francerz\Http\BodyParsers;
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
}