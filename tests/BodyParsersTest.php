<?php

use Francerz\Http\BodyParsers;
use Francerz\Http\Parsers\JsonParser;
use Francerz\Http\Parsers\UrlEncodedParser;
use PHPUnit\Framework\TestCase;

class BodyParsersTest extends TestCase
{
    public function testUrlEncoded()
    {
        BodyParsers::register(new UrlEncodedParser());

        $type = 'application/x-www-form-urlencoded';
        $parser = BodyParsers::find($type);

        $data = array('foo'=>1, 'bar'=>2);
        
        $encoded = $parser->encode($data);
        $this->assertEquals('foo=1&bar=2', $encoded);

        $redata = $parser->decode($encoded);
        $this->assertEquals($data, $redata);
    }

    public function testJson()
    {
        BodyParsers::register(new JsonParser());

        $type = 'application/json';
        $parser = BodyParsers::find($type);
        
        $data = new stdClass();
        $data->foo = 1;
        $data->bar = 2;

        $encoded = $parser->encode($data);
        $this->assertEquals('{"foo":1,"bar":2}', $encoded);

        $redata = $parser->decode($encoded);
        $this->assertEquals($data, $redata);
    }
}