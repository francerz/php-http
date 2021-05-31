<?php

namespace Francerz\Http;

use Francerz\Http\Utils\HttpHelper;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UploadedFileInterface;
use RuntimeException;

class UploadedFile implements UploadedFileInterface
{
    private $streamPath;
    private $stream;
    private $error = 0;
    private $name = null;
    private $type = null;
    private $size = 0;

    private $moved = false;

    public function __construct(
        StreamInterface $stream,
        ?int $size = null,
        int $error = UPLOAD_ERR_OK,
        ?string $name = null,
        ?string $type = null
    ) {
        $this->stream = $stream;
        $this->size = $size;
        $this->error = $error;
        $this->name = $name;
        $this->type = $type;

        $this->streamPath = $stream->getMetadata('uri');
    }

    public function getStream()
    {
        if ($this->moved) {
            throw new RuntimeException('Cannot find stream source');
        }
        return $this->stream;  
    }

    public function moveTo($targetPath)
    {
        if (is_null($this->streamPath)) {
            throw new RuntimeException('FileStream without source path');
        }

        if (!file_exists($this->streamPath)) {
            throw new RuntimeException("Cannot find file in '{$this->streamPath}'");
        }

        move_uploaded_file($this->streamPath, $targetPath);
        $this->moved = true;
    }

    public function getSize()
    {
        return $this->size;
    }

    public function getError()
    {
        return $this->error;
    }

    public function getClientFilename()
    {
        return $this->name;
    }

    public function getClientMediaType()
    {
        return $this->type;
    }
}