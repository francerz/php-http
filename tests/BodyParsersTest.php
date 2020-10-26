<?php

use Francerz\Http\HttpFactory;
use Francerz\Http\Utils\BodyParserHandler;
use Francerz\Http\Utils\Constants\MediaTypes;
use PHPUnit\Framework\TestCase;

class BodyParsersTest extends TestCase
{
    public function testUrlEncoded()
    {
        $httpFactory = new HttpFactory();
        $type = MediaTypes::APPLICATION_X_WWW_FORM_URLENCODED;
        $parser = BodyParserHandler::find($type);

        $data = array('foo'=>1, 'bar'=>2);
        
        $serialized = $parser->unparse($httpFactory, $data);
        $this->assertEquals('foo=1&bar=2', $serialized);

        $redata = $parser->parse($serialized);
        $this->assertEquals($data, $redata);
    }

    public function testJson()
    {
        $httpFactory= new HttpFactory();
        $type = MediaTypes::APPLICATION_JSON;
        $parser = BodyParserHandler::find($type);
        
        $data = new stdClass();
        $data->foo = 1;
        $data->bar = 2;

        $serialized = $parser->unparse($httpFactory, $data);
        $this->assertEquals('{"foo":1,"bar":2}', $serialized);

        $redata = $parser->parse($serialized);
        $this->assertEquals($data, $redata);
    }
}