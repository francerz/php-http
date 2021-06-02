<?php

use Francerz\Http\FileStream;
use PHPUnit\Framework\TestCase;

class FileStreamTest extends TestCase
{
    public function testFileStreamInput()
    {
        $fs = new FileStream('php://input');

        $this->assertInstanceOf(FileStream::class, $fs);
        $this->assertEmpty((string)$fs);
    }

    public function testFileStreamTemp()
    {
        $fs = new FileStream('php://temp', 'r+');

        $this->assertInstanceOf(FileStream::class, $fs);
        $this->assertEmpty((string)$fs);
        
        $writen = $fs->write('Hello');
        $this->assertEquals(5, $writen);
        $fs->seek(2);
        $this->assertEquals('llo', $fs->getContents());
        $this->assertEquals('Hello', (string)$fs);
        
    }
}