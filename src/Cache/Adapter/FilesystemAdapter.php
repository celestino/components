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
