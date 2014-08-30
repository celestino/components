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

namespace Brickoo\Component\Cache\Adapter;

use Brickoo\Component\Validation\Argument;
use DirectoryIterator;

/**
 * FilesystemAdapter
 *
 * Provides caching operations based on filesystem.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class FilesystemAdapter implements Adapter {

    /** @var string */
    const LIFETIME_FORMAT = "YmdHis";

    /** @var string */
    private $cacheDirectory;

    /** @var boolean */
    private $serializeCacheContent;

    /** @var string */
    private $cacheFileNameSuffix;

    /**
     * Class constructor.
     * @param string $cacheDirectory the directory used for the cache operations
     * @param boolean $serializeCacheContent flag to serialize the content cached
     * @param string $cacheFileNameSuffix the suffix to add to caching file names
     * @throws \InvalidArgumentException if an argument is not valid
     */
    public function __construct($cacheDirectory, $serializeCacheContent = true, $cacheFileNameSuffix = ".cache") {
        Argument::isString($cacheDirectory);
        Argument::isBoolean($serializeCacheContent);
        Argument::isString($cacheFileNameSuffix);

        $this->cacheDirectory = rtrim($cacheDirectory, "\\/").DIRECTORY_SEPARATOR;
        $this->serializeCacheContent = $serializeCacheContent;
        $this->cacheFileNameSuffix = $cacheFileNameSuffix;
    }

    /** {@inheritDoc} */
    public function get($identifier) {
        Argument::isString($identifier);
        $timestampBytesLength = strlen(date(self::LIFETIME_FORMAT));
        $cacheFilePath = $this->getCacheFilePath($identifier);

        $file = fopen($cacheFilePath, "r");
        $expirationDate = fread($file, $timestampBytesLength);

        if (strtotime($expirationDate) < time()) {
            fclose($file);
            return null;
        }

        $cachedContent = fread($file, filesize($cacheFilePath) - $timestampBytesLength);
        fclose($file);

        return ($this->serializeCacheContent ? unserialize($cachedContent) : $cachedContent);
    }

    /** {@inheritDoc} */
    public function set($identifier, $content, $lifetime) {
        Argument::isString($identifier);
        Argument::isInteger($lifetime);

        if ($this->serializeCacheContent) {
            $content = serialize($content);
        }

        $file = fopen($this->getCacheFilePath($identifier), "w");
        fwrite($file, date(self::LIFETIME_FORMAT, (time() + $lifetime)).$content);
        fclose($file);

        return $this;
    }

    /** {@inheritDoc} */
    public function delete($identifier) {
        Argument::isString($identifier);
        if (file_exists(($fileName = $this->getCacheFilePath($identifier)))) {
            unlink($fileName);
        }
        return $this;
    }

    /** {@inheritDoc} */
    public function flush() {
        $directoryIterator = new DirectoryIterator($this->cacheDirectory);
        foreach ($directoryIterator as $fileInfo) {
            if ($fileInfo->isFile()
                && preg_match(sprintf("~%s$~", $this->cacheFileNameSuffix), $fileInfo->getFilename())) {
                    unlink($fileInfo->getPathname());
            }
        }
        return $this;
    }

    /** {@inheritDoc} */
    public function isReady() {
        return (is_writable($this->cacheDirectory) && is_readable($this->cacheDirectory));
    }

    /**
     * Returns the cache file path for a unique identifier.
     * @param string $identifier the cache identifier.
     * @return string the cache file path
     */
    private function getCacheFilePath($identifier) {
        return $this->cacheDirectory.$identifier.$this->cacheFileNameSuffix;
    }

}
