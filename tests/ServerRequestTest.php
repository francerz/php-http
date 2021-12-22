<?php

namespace Francerz\Http\Tests;

use Francerz\Http\ServerRequest;
use Francerz\Http\Uri;
use PHPUnit\Framework\TestCase;

class ServerRequestTest extends TestCase
{
    public function testInstantiation()
    {
        $uri = new Uri('http://example.com/path/to/resource');
        $request = new ServerRequest($uri);

        $this->assertInstanceOf(ServerRequest::class, $request);

        $request = $request->withParsedBody(['a' => 1, 'b' => 2]);
        $this->assertEquals(['a' => 1, 'b' => 2], $request->getParsedBody());
    }
}
