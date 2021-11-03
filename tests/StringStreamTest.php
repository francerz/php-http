<?php

namespace Tests;

use Francerz\Http\StringStream;
use PHPUnit\Framework\TestCase;

class StringStreamTest extends TestCase
{
    public function testCreateInstance()
    {
        $string = new StringStream('hello');

        $this->assertEquals('hello', (string)$string);
        $this->assertEquals(5, $string->getSize());
        $this->assertEquals(0, $string->tell());
        $this->assertTrue($string->isSeekable());
        $this->assertTrue($string->isWritable());
        $this->assertTrue($string->isReadable());
        $this->assertFalse($string->eof());
        return $string;
    }

    /**
     * @depends testCreateInstance
     */
    public function testWrites(StringStream $string)
    {
        $string->seek(0, SEEK_END);
        $this->assertEquals(5, $string->tell());

        $string->write('world');
        $this->assertEquals('helloworld', (string)$string);
        $this->assertEquals(10, $string->getSize());
        $this->assertEquals(10, $string->tell());
        $this->assertTrue($string->eof());

        $string->seek(5, SEEK_SET);
        $this->assertEquals(5, $string->tell());
        $this->assertFalse($string->eof());

        $remain = $string->getContents();
        $this->assertEquals('world', $remain);
        $this->assertEquals(10, $string->tell());
        $this->assertTrue($string->eof());

        $string->seek(strlen($remain) * -1, SEEK_CUR);
        $this->assertEquals(5, $string->tell());

        $string->write(' ' . $remain);
        $this->assertEquals('hello world', (string)$string);
        $this->assertEquals(11, $string->getSize());
        $this->assertEquals(11, $string->tell());
        $this->assertTrue($string->eof());

        return $string;
    }

    /**
     * @depends testWrites
     *
     * @param StringStream $string
     */
    public function testAppend(StringStream $string)
    {
        $string->rewind();
        $this->assertEquals(0, $string->tell());

        $string->append('!');
        $this->assertEquals('hello world!', (string)$string);
        $this->assertEquals(12, $string->getSize());
        $this->assertEquals(12, $string->tell());

        return $string;
    }

    /**
     * @depends testAppend
     *
     * @param StringStream $string
     * @return void
     */
    public function testInsert(StringStream $string)
    {
        $string->seek(6);
        $this->assertEquals(6, $string->tell());

        $string->insert('to all the ');
        $this->assertEquals('hello to all the world!', (string)$string);
        $this->assertEquals(23, $string->getSize());
        $this->assertEquals(17, $string->tell());
        $this->assertFalse($string->eof());
    }
}
