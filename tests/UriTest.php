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

        return $uri;
    }

    /**
     * @depends testParsing
     *
     * @param Uri $uri
     * @return void
     */
    public function testQueryParams(Uri $uri)
    {
        $this->assertEquals('string', $uri->getQueryParam('query'));
        $this->assertNull($uri->getQueryParam('attr2'));

        $uri2 = $uri->withQueryParam('attr2', 'new_value');

        $this->assertEquals('string', $uri2->getQueryParam('query'));
        $this->assertEquals('new_value', $uri2->getQueryParam('attr2'));

        $this->assertNull($uri->getQueryParam('attr2'));
        $this->assertEquals(
            'https://user:pass@www.example.com:8080/path/to/doc?query=string&attr2=new_value#fragment',
            (string)$uri2
        );

        $uri3 = $uri2->withoutQueryParam('query');

        $this->assertNull($uri3->getQueryParam('query'));
        $this->assertEquals(
            'https://user:pass@www.example.com:8080/path/to/doc?attr2=new_value#fragment',
            (string)$uri3
        );

        $uri4 = $uri3->withQueryParams(['attr1'=>1], false);
        $this->assertEquals(['attr2'=>'new_value','attr1'=>1], $uri4->getQueryParams());
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