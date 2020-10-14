<?php

namespace Francerz\Http;

use Psr\Http\Message\StreamInterface;

class ResourceStream implements StreamInterface
{
    public function __toString()
    {
        return '';
    }
    public function close()
    {

    }
    public function detach()
    {
        
    }
    public function getSize()
    {
        return 0;
    }
    public function tell()
    {
        return 0;
    }
    public function eof()
    {
        return true;
    }
    public function isSeekable()
    {
        return false;
    }
    public function seek($offset, $whence = SEEK_SET)
    {
        
    }
    public function rewind()
    {
        
    }
    public function isWritable()
    {
        return false;
    }
    public function write($string)
    {
        
    }
    public function isReadable()
    {
        
    }
    public function read($length)
    {
        return null;
    }
    public function getContents()
    {
        
    }
    public function getMetadata($key = null)
    {
        
    }
}