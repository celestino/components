<?php

/*
 * Copyright (c) 2011-2013, Celestino Diaz <celestino.diaz@gmx.de>.
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

namespace Brickoo\Cache\Adapter;

use Brickoo\Cache\Adapter,
    Brickoo\Filesystem\FileObject,
    Brickoo\Validator\Argument;

/**
 * FilesystemAdapter
 *
 * Provides caching operations based on files.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class FilesystemAdapter implements Adapter {

    /** @var string */
    const LIFETIME_FORMAT = "YmdHis";

    /** @var integer */
    const LIFETIME_BYTES_LENGTH = 14;

    /** @var \Brickoo\System\FileObject */
    private $fileObject;

    /** @var string */
    private $cacheDirectory;

    /** @var boolean */
    private $serializeCacheContent;

    /** @var string */
    private $cacheFileNameSuffix;

    /**
     * Class constructor.
     * @param \Brickoo\Filesystem\FileObject $fileObject
     * @param string $cacheDirectory the directory used for the cache operations
     * @param boolean $serializeCacheContent flag to serialize the content cached
     * @param string $cacheFileNameSuffix the sufffix to add to caching file names
     * @throws \InvalidArgumentException if an argument is not valid
     * @return void
     */
    public function __construct(FileObject $fileObject, $cacheDirectory, $serializeCacheContent = true, $cacheFileNameSuffix = ".cache") {
        Argument::IsString($cacheDirectory);
        Argument::IsBoolean($serializeCacheContent);
        Argument::IsString($cacheFileNameSuffix);

        $this->fileObject = $fileObject;
        $this->cacheDirectory = rtrim($cacheDirectory, "\\/") . DIRECTORY_SEPARATOR;
        $this->serializeCacheContent = $serializeCacheContent;
        $this->cacheFileNameSuffix = $cacheFileNameSuffix;
    }

    /**
     * @param string $identifier the cache identifier.
     * @return string the cache file path
     */
    private function getCacheFilePath($identifier) {
        return $this->cacheDirectory . $identifier .$this->cacheFileNameSuffix;
    }

    /** {@inheritDoc} */
    public function get($identifier) {
        Argument::IsString($identifier);
        $cacheFilePath = $this->getCacheFilePath($identifier);

        if (! is_readable($cacheFilePath)) {
            return null;
        }

        $expirationDate = $this->fileObject->open($cacheFilePath, "r")->read(self::LIFETIME_BYTES_LENGTH);

        if (strtotime($expirationDate) < time()) {
            $this->fileObject->close();
            return null;
        }

        $cachedContent = $this->fileObject->read(filesize($cacheFilePath) - self::LIFETIME_BYTES_LENGTH);
        $this->fileObject->close();

        return ($this->serializeCacheContent ? unserialize($cachedContent) : $cachedContent);
    }

    /**
     * {@inheritDoc}
     */
    public function set($identifier, $content, $lifetime) {
        Argument::IsString($identifier);
        Argument::IsInteger($lifetime);

        if ($this->serializeCacheContent) {
            $content = serialize($content);
        }

        $this->fileObject->open($this->getCacheFilePath($identifier), "w")
                         ->write(date(self::LIFETIME_FORMAT, (time()+ $lifetime)) . $content);
        $this->fileObject->close();

        return $this;
    }

    /** {@inheritDoc} */
    public function delete($identifier) {
        Argument::IsString($identifier);

        if (file_exists(($fileName = $this->getCacheFilePath($identifier)))) {
            unlink($fileName);
        }

        return $this;
    }

    /** {@inheritDoc} */
    public function flush() {
        $DirectoryIterator = new \DirectoryIterator($this->cacheDirectory);

        foreach ($DirectoryIterator as $FileInfo) {
            if ($FileInfo->isFile()
                && ($fileName = $FileInfo->getFilename())
                && preg_match(sprintf("~%s$~", $this->cacheFileNameSuffix), $fileName)
            ){
                unlink($FileInfo->getPath() . DIRECTORY_SEPARATOR . $fileName);
            }
        }

        return $this;
    }

    /** {@inheritDoc} */
    public function isReady() {
        return (is_writable($this->cacheDirectory) && is_readable($this->cacheDirectory));
    }

}