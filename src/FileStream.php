<?php

namespace Francerz\Http;

use Psr\Http\Message\StreamInterface;

class FileStream implements StreamInterface
{
    private $path;
    private $mode;
    private $handle;

    public function __construct($path, $openmode = 'r')
    {
        $this->path   = $path;
        $this->mode   = $openmode;
        $this->handle = fopen($path, $openmode);
    }
    
    public function getPath()
    {
        return $this->path;
    }
    public function getMode()
    {
        return $this->mode;
    }

    #region StreamInterface implementations
    public function __toString()
    {
        return stream_get_contents($this->handle, -1, 0);
    }
    public function close()
    {
        fclose($this->handle);
    }
    public function detach()
    {
        $handle = $this->handle;
        $this->handle = null;
        return $handle;
    }
    public function getSize() : ?int
    {
        return filesize($this->path);
    }
    public function tell()
    {
        return ftell($this->handle);
    }
    public function eof() : bool
    {
        return feof($this->handle);
    }
    public function isSeekable()
    {
        return fseek($this->handle, 0, SEEK_CUR) !== -1;
    }
    public function seek($offset, $whence = SEEK_SET)
    {
        fseek($this->handle, $offset, $whence);
    }
    public function rewind()
    {
        rewind($this->handle);
    }
    public function isWritable()
    {
        return is_writable($this->path);
    }
    public function write($string) : int
    {
        $written = fwrite($this->handle, $string);
        if ($written === false) {
            throw new \RuntimeException('Error writing file contents.');
        }
        return $written;
    }
    public function isReadable() : bool
    {
        return is_readable($this->path);
    }
    public function read($length) : string
    {
        $string = fread($this->handle, $length);
        if ($string === false) {
            throw new \RuntimeException('Error reading file contents.');
        }
        return $string;
    }
    public function getContents()
    {
        $string = fread($this->handle, 0);
        if ($string === false) {
            throw new \RuntimeException('Unable to read contents.');
        }
        return $string;
    }
    public function getMetadata($key = null)
    {
        $meta = stream_get_meta_data($this->handle);

        if (is_array($meta) && array_key_exists($key, $meta)) {
            return $meta[$key];
        }

        return $meta;
    }
    #endregion
}