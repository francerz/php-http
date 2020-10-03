<?php

namespace Francerz\Http\Parsers;

use Francerz\Http\ParserInterface;
use Francerz\Http\StringStream;
use Psr\Http\Message\StreamInterface;

class JsonParser implements ParserInterface
{
    public function getSupportedTypes(): array
    {
        return ['application/json'];
    }

    public function decode(StreamInterface $content, string $args = '')
    {
        return json_decode((string)$content);
    }
    
    public function encode($content, string $args = '') : StreamInterface
    {
        return new StringStream(json_encode($content));
    }
}