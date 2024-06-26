<?php

namespace Francerz\Http;

use Psr\Http\Message\StreamInterface;

class StringStream implements StreamInterface
{
    private $string;
    private $pointer;

    public function __construct($string = '')
    {
        $this->pointer = 0;
        $this->string = $string;
    }

    #region StreamInterface implementation
    public function __toString(): string
    {
        return $this->string;
    }

    public function close(): void
    {
    }

    public function detach()
    {
        $string = $this->string;
        $this->string = '';
        return $string;
    }

    public function getSize(): int
    {
        return strlen($this->string);
    }

    public function tell(): int
    {
        return $this->pointer;
    }

    public function eof(): bool
    {
        return $this->pointer >= strlen($this->string);
    }

    public function isSeekable(): bool
    {
        return true;
    }

    public function seek($offset, $whence = SEEK_SET): void
    {
        switch ($whence) {
            case SEEK_SET:
                $this->pointer = $offset;
                break;
            case SEEK_CUR:
                $this->pointer += $offset;
                break;
            case SEEK_END:
                $this->pointer = strlen($this->string) + $offset;
                break;
        }
    }

    public function rewind(): void
    {
        $this->seek(0);
    }

    public function isWritable(): bool
    {
        return true;
    }

    public function write($string): int
    {
        $this->string = substr_replace(
            $this->string,
            $string,
            $this->pointer,
            strlen($string)
        );
        $this->pointer += strlen($string);
        return strlen($string);
    }

    public function isReadable(): bool
    {
        return true;
    }

    public function read($length): string
    {
        if ($this->pointer >= strlen($this->string)) {
            return '';
        }
        $ret = substr($this->string, $this->pointer, $length);
        $this->pointer += $length;
        return $ret;
    }

    public function getContents(): string
    {
        $ret = substr($this->string, $this->pointer);
        $this->pointer = strlen($this->string);
        return $ret;
    }

    public function getMetadata($key = null)
    {
        if (is_null($key)) {
            return array();
        }
        return null;
    }
    #endregion

    /**
     * @deprecated v0.3.0
     *
     * @param string $string
     * @return void
     */
    public function append($string)
    {
        $this->seek(0, SEEK_END);
        $this->write($string);
    }

    /**
     * @deprecated v0.3.0
     *
     * @param array|string $string
     * @return void
     */
    public function insert($string)
    {
        $this->string = substr_replace($this->string, $string, $this->pointer, 0);
        $this->pointer += strlen($string);
    }
}
