<?php

/*
 * Copyright (c) 2011-2014, Celestino Diaz <celestino.diaz@gmx.de>.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 * 1. Redistributions of source code must retain the above copyright
 *    notice, this list of conditions and the following disclaimer.
 * 2. Redistributions in binary form must reproduce the above copyright
 *    notice, this list of conditions and the following disclaimer in the
 *    documentation and/or other materials provided with the distribution.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */
namespace Brickoo\Component\IO\Stream;

use Brickoo\Component\IO\Stream\Exception\AccessModeUnknownException;
use Brickoo\Component\IO\Stream\Exception\UnableToCreateResourceHandleException;

/**
 * FileStream
 *
 * Implementation of a stream based on a file resource.
 */
class FileStream implements Stream {

    /** @cons file flags */
    const MODE_READ = 1;
    const MODE_WRITE = 2;
    const MODE_APPEND = 4;
    const MODE_RESET = 8;
    const MODE_COOP = 16;

    /** @var resource */
    private $resource;

    /** @var \Brickoo\Component\IO\Stream\FileStreamConfig */
    private $streamConfig;

    /** @var array */
    private $availableModes;

    /** @param \Brickoo\Component\IO\Stream\FileStreamConfig $streamConfig */
    public function __construct(FileStreamConfig $streamConfig) {
        $this->streamConfig = $streamConfig;

        $this->availableModes = [
            self::MODE_READ => "rb",
            self::MODE_WRITE => "wb",
            self::MODE_READ + self::MODE_WRITE => "rb+",
            self::MODE_WRITE + self::MODE_RESET => "wb+",
            self::MODE_WRITE + self::MODE_APPEND => "ab",
            self::MODE_READ + self::MODE_WRITE + self::MODE_APPEND => "ab+",
            self::MODE_WRITE + self::MODE_COOP => "cb",
            self::MODE_READ + self::MODE_WRITE + self::MODE_COOP => "cb+"
        ];
    }

    /**
     * Returns the file stream configuration.
     * @return FileStreamConfig
     */
    public function getConfiguration() {
        return $this->streamConfig;
    }

    /**
     * Reconfigure the file stream context.
     * @param FileStreamConfig $streamConfig
     * @return \Brickoo\Component\IO\Stream\FileStream
     */
    public function reconfigure(FileStreamConfig $streamConfig) {
        $this->streamConfig = $streamConfig;
        return $this;
    }

    /** {@inheritdoc} */
    public function open() {
        if ($this->hasResource()) {
            return $this->resource;
        }

        $configuration = $this->getConfiguration();

        if (! ($resource = @fopen(
            $configuration->getFilename(),
            $this->resolveMode($configuration->getMode()),
            $configuration->shouldUseIncludePath(),
            stream_context_create($configuration->getContext())
        ))) {
            throw new UnableToCreateResourceHandleException($this->getConfiguration()->getFilename());
        }

        $this->resource = $resource;
        return $this->resource;
    }

    /** {@inheritdoc} */
    public function close() {
        if ($this->hasResource()) {
            fclose($this->resource);
            $this->resource = null;
        }
    }

    /** Release stream resource on destruction. */
    public function __destruct() {
        $this->close();
    }

    /**
     * Returns the corresponding file access mode,,.
     * @param integer $mode
     * @throws \Brickoo\Component\IO\Stream\Exception\AccessModeUnknownException
     * @return string the resolved mode
     */
    private function resolveMode($mode) {
        if (! isset($this->availableModes[$mode])) {
            throw new AccessModeUnknownException($mode);
        }
        return $this->availableModes[$mode];
    }

    /**
     * Checks if the resource has been already created.
     * @return boolean check result
     */
    private function hasResource() {
        return is_resource($this->resource);
    }

}
