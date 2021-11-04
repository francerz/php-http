<?php

namespace Francerz\Http;

use Psr\Http\Message\StreamInterface;

class ResourceStream implements StreamInterface
{
    /** @var resource */
    private $stream;
    /**
     * @param resource $stream
     */
    public function __construct($stream)
    {
        $this->stream = $stream;
    }
    public function __toString()
    {
        return fgetc($this->stream);
    }
    public function close()
    {
        fclose($this->stream);
    }
    public function detach()
    {
        return null;
    }
    public function getSize()
    {
        return null;
    }
    public function tell()
    {
        return ftell($this->stream);
    }
    public function eof()
    {
        return feof($this->stream);
    }
    public function isSeekable()
    {
        return $this->getMetadata('seekable') ?? false;
    }
    public function seek($offset, $whence = SEEK_SET)
    {
        fseek($this->stream, $offset, $whence);
    }
    public function rewind()
    {
        fseek($this->stream, 0, SEEK_SET);
    }
    public function isWritable()
    {
        $mode = $this->getMetadata('mode');
        return strpos($mode, 'w') !== false;
    }
    public function write($string)
    {
        fwrite($this->stream, $string);
    }
    public function isReadable()
    {
        $mode = $this->getMetadata('mode');
        return strpos($mode, 'r') !== false;
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
        $meta = stream_get_meta_data($this->stream);
        if (is_null($key)) {
            return $meta;
        }
        return $meta[$key] ?? null;
    }
}
