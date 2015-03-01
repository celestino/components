<?php

/*
 * Copyright (c) 2011-2015, Celestino Diaz <celestino.diaz@gmx.de>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace Brickoo\Component\IO\Stream;

use Brickoo\Component\IO\Stream\Exception\AccessModeUnknownException;
use Brickoo\Component\IO\Stream\Exception\UnableToCreateResourceHandleException;

/**
 * FileStream
 *
 * Implementation of a stream based on a file resource.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
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

        if (!($resource = @fopen(
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
        if (!isset($this->availableModes[$mode])) {
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
