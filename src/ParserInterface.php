<?php

namespace Francerz\Http;

use Psr\Http\Message\StreamInterface;

interface ParserInterface
{
    public function getSupportedTypes() : array;
    public function decode(StreamInterface $content, string $args = '');
    public function encode($content, string $args = '') : StreamInterface;
}