<?php

namespace Francerz\Http;

use Psr\Http\Message\StreamInterface;
use RuntimeException;

class FileStream implements StreamInterface
{
    private $path;
    private $mode;
    private $handle = null;

    public function __construct($path, $openmode = 'r')
    {
        $this->path   = $path;
        $this->mode   = $openmode;
        if (in_array($path, ['php://input','php://temp']) || file_exists($path)) {
            $this->handle = fopen($path, $openmode);
        }
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
        if (isset($this->handle)) {
            return;
        }
        fclose($this->handle);
    }

    public function detach()
    {
        $handle = $this->handle;
        $this->handle = null;
        return $handle;
    }

    public function getSize(): ?int
    {
        return filesize($this->path);
    }

    public function tell()
    {
        if (is_null($this->handle)) {
            throw new RuntimeException('Invalid handle.');
        }
        return ftell($this->handle);
    }

    public function eof(): bool
    {
        if (is_null($this->handle)) {
            return true;
        }
        return feof($this->handle);
    }

    public function isSeekable()
    {
        if (is_null($this->handle)) {
            return false;
        }
        return fseek($this->handle, 0, SEEK_CUR) !== -1;
    }

    public function seek($offset, $whence = SEEK_SET)
    {
        if (is_null($this->handle)) {
            return;
        }
        fseek($this->handle, $offset, $whence);
    }

    public function rewind()
    {
        if (is_null($this->handle)) {
            throw new RuntimeException('Invalid handle');
        }
        $rewinded = rewind($this->handle);
        if ($rewinded === false) {
            throw new RuntimeException('Cannot rewind stream');
        }
    }

    public function isWritable()
    {
        return file_exists($this->path) && is_writable($this->path);
    }

    public function write($string): int
    {
        if (is_null($this->handle)) {
            throw new RuntimeException('Invalid handle');
        }
        $written = fwrite($this->handle, $string);
        if ($written === false) {
            throw new \RuntimeException('Error writing file contents.');
        }
        return $written;
    }

    public function isReadable(): bool
    {
        return file_exists($this->path) && is_readable($this->path);
    }

    public function read($length): string
    {
        if (is_null($this->handle)) {
            throw new RuntimeException('Invalid handle');
        }
        $string = fread($this->handle, $length);
        if ($string === false) {
            throw new \RuntimeException('Error reading file contents.');
        }
        return $string;
    }

    public function getContents()
    {
        if (is_null($this->handle)) {
            throw new RuntimeException('Invalid handle.');
        }
        $string = stream_get_contents($this->handle);
        if ($string === false) {
            throw new \RuntimeException('Unable to read contents.');
        }
        return $string;
    }

    public function getMetadata($key = null)
    {
        if (is_null($this->handle)) {
            return is_null($key) ? [] : null;
        }
        $meta = stream_get_meta_data($this->handle);

        if (is_array($meta) && array_key_exists($key, $meta)) {
            return $meta[$key];
        }

        return $meta;
    }
    #endregion
}
