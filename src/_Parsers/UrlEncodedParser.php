<?php

namespace Francerz\Http\Parsers;

use Francerz\Http\ParserInterface;
use Francerz\Http\StringStream;
use Psr\Http\Message\StreamInterface;

class UrlEncodedParser implements ParserInterface
{
    public function getSupportedTypes(): array
    {
        return ['application/x-www-form-urlencoded'];
    }

    public function decode(StreamInterface $content, string $args = '')
    {
        parse_str((string)$content, $result);
        return $result;
    }

    public function encode($content, string $args = '') : StreamInterface
    {
        return new StringStream(http_build_query($content));
    }
}