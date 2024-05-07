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
    public function close(): void
    {
        fclose($this->stream);
    }
    public function detach()
    {
        unset($this->stream);
    }
    public function getSize(): int
    {
        if (!isset($this->stream)) {
            return null;
        }

        $stats = fstat($this->stream);
        return $stats['size'] ?? null;
    }
    public function tell(): int
    {
        return ftell($this->stream);
    }
    public function eof(): bool
    {
        return feof($this->stream);
    }
    public function isSeekable(): bool
    {
        return $this->getMetadata('seekable') ?? false;
    }
    public function seek($offset, $whence = SEEK_SET): void
    {
        fseek($this->stream, $offset, $whence);
    }
    public function rewind(): void
    {
        fseek($this->stream, 0, SEEK_SET);
    }
    public function isWritable(): bool
    {
        $mode = $this->getMetadata('mode');
        return strpos($mode, 'w') !== false;
    }
    public function write(string $string): int
    {
        return fwrite($this->stream, $string);
    }
    public function isReadable(): bool
    {
        $mode = $this->getMetadata('mode');
        return strpos($mode, 'r') !== false;
    }

    public function read(int $length): string
    {
        $string = fread($this->stream, $length);
        if ($string === false) {
            return '';
        }
        return $string;
    }
    public function getContents(): string
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
