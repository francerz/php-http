<?php

use Francerz\Http\Uri;
use PHPUnit\Framework\TestCase;

class UriTest extends TestCase
{
    public function testBasicUriBuild()
    {
        $uri = new Uri();
        $this->assertEquals('', (string) $uri);

        return $uri;
    }

    /**
     * @depends testBasicUriBuild
     *
     * @param Uri $uri
     * @return void
     */
    public function testExampleDotCom(Uri $uri)
    {
        $uri = $uri->withScheme('http');
        $uri = $uri->withHost('www.example.com');

        $this->assertEquals('http://www.example.com', (string) $uri);

        return $uri;
    }

    /**
     * @depends testExampleDotCom
     */
    public function testWithRootlessPath(Uri $uri)
    {
        $uri = $uri->withPath('path/to/file');

        $this->assertEquals('http://www.example.com/path/to/file', (string) $uri);

        return $uri;
    }

    /**
     * @depends testWithRootlessPath
     */
    public function testRemovingHost(Uri $uri)
    {
        $uri = $uri->withHost('');

        $this->assertEquals('http:/path/to/file', (string) $uri);

        return $uri;
    }

    public function testParsing()
    {
        $url = 'https://user:pass@www.example.com:8080/path/to/doc?query=string#fragment';
        $uri = new Uri($url);
        
        $this->assertEquals('https', $uri->getScheme());
        $this->assertEquals('user:pass', $uri->getUserInfo());
        $this->assertEquals('www.example.com', $uri->getHost());
        $this->assertEquals(8080, $uri->getPort());
        $this->assertEquals('/path/to/doc', $uri->getPath());
        $this->assertEquals('query=string', $uri->getQuery());
        $this->assertEquals('fragment', $uri->getFragment());

        $this->assertEquals($url, (string) $uri);
    }

    public function testParsingMailTo()
    {
        // Why mailto scheme doesn't have '//' ?
        $mail = 'mailto://user@example.com';
        $uri = new Uri($mail);

        $this->assertEquals('mailto', $uri->getScheme());
        $this->assertEquals('user', $uri->getUserInfo());
        $this->assertEquals('example.com', $uri->getHost());


    }
}