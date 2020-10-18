<?php

use Francerz\Http\Tools\UriHelper;
use Francerz\Http\Uri;
use PHPUnit\Framework\TestCase;

class UriHelperTest extends TestCase
{
    public function testAppendPath()
    {
        $uri = new Uri('http://www.example.com');

        $uri = UriHelper::appendPath($uri, 'a');
        $this->assertEquals('http://www.example.com/a', (string)$uri);

        $uri = UriHelper::appendPath($uri, 'b/');
        $this->assertEquals('http://www.example.com/a/b/', (string)$uri);

        $uri = UriHelper::appendPath($uri, '/c');
        $this->assertEquals('http://www.example.com/a/b/c', (string)$uri);

        $uri = UriHelper::appendPath($uri, '//d');
        $this->assertEquals('http://www.example.com/a/b/c//d', (string)$uri);
    }
}