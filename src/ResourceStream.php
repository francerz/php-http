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
        return stream_get_contents($this->stream);
    }
    public function close()
    {
        fclose($this->stream);
    }
    public function detach()
    {
        unset($this->stream);
    }
    public function getSize()
    {
        if (!isset($this->stream)) {
            return null;
        }

        $stats = fstat($this->stream);
        return $stats['size'] ?? null;
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
        $string = fread($this->stream, $length);
        if ($string === false) {
            return '';
        }
        return $string;
    }
    public function getContents()
    {
        $contents = stream_get_contents($this->stream);
        if ($contents === false) {
            return '';
        }
        return $contents;
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
