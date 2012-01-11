<?php

    /*
     * Copyright (c) 2011-2012, Celestino Diaz <celestino.diaz@gmx.de>.
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
     * 3. Neither the name of Brickoo nor the names of its contributors may be used
     *    to endorse or promote products derived from this software without specific
     *    prior written permission.
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

   namespace Brickoo\Library\Cache\Provider;

    use Brickoo\Library\Core;
    use Brickoo\Library\System;
    use Brickoo\Library\Cache\Interfaces;
    use Brickoo\Library\Validator\TypeValidator;

    /**
     * FileProvider
     *
     * Provides caching operations based on the Fileystem.
     * Uses the System\FileObject dependecy to handle the file operations.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class FileProvider implements Interfaces\CacheProviderInterface
    {

        /**
         * Holds the lifetime format used for storing the expiration.
         * @var string
         */
        const LIFETIME_FORMAT = 'YmdHis';

        /**
         * Holds the amount of bytes the timestamp consumes.
         * @var integer
         */
        const LIFETIME_BYTES = 14;

        /**
         * Holds the FileObject dependency.
         * @var Brickoo\Library\System\Interfaces\FileObjectInterface FileObject
         */
        protected $FileObject;

        /**
         * Lazy initialization of the FileObject.
         * Returns the FileObject dependency.
         * @return object FileObject implementing the System\Interface\FileObjectInterface
         */
        public function getFileObject()
        {
            if (! $this->FileObject instanceof System\Interfaces\FileObjectInterface)
            {
                $this->FileObject = new System\FileObject();
            }

            return $this->FileObject;
        }

        /**
         * Injects a FileObject dependency.
         * @throws Brickoo\Library\Core\Exceptions\DependencyOverwriteException if trying to overwrite the dependency
         * @param \Brickoo\Library\System\Interfaces\FileObjectInterface $FileObject the FileObject to inject
         * @return object reference
         */
        public function injectFileObject(\Brickoo\Library\System\Interfaces\FileObjectInterface $FileObject)
        {
            if ($this->FileObject !== null)
            {
                throw new Core\Exceptions\DependencyOverwriteException('FileObjectInterface');
            }

            $this->FileObject = $FileObject;

            return $this;
        }

        /**
         * Holds the file prefix to use.
         * @var string
         */
        protected $filePrefix;

        /**
         * Returns the file prefix used.
         * @return string the file prefix used
         */
        public function getFilePrefix()
        {
            return $this->filePrefix;
        }

        /**
         * Sets the file prefix which does match([\w]+).
         * @param string $filePrefix the file rpefix to use
         * @throws UnexpectedValueException if the file prefix does not match the regular expression
         * @return object reference
         */
        public function setFilePrefix($filePrefix)
        {
            TypeValidator::IsString($filePrefix);

            if (! preg_match('~^[\w]+$~', $filePrefix))
            {
                throw new \UnexpectedValueException(sprintf('The file prefix `%s` is not valid.', $filePrefix));
            }

            $this->filePrefix = $filePrefix;

            return $this;
        }

        /**
         * Holds the directory to write cached files to.
         * @var string
         */
        protected $directory;

        /**
         * Returns the directory where the cached files are stored in.
         * If the the directory has not been defined, the PHP temporary directory will be used
         * @return string the cache Directory
         */
        public function getDirectory()
        {
            if ($this->directory === null)
            {
                $this->setDirectory(sys_get_temp_dir());
            }

            return $this->directory;
        }

        /**
         * Sets the directory to use for caching.
         * @param strig $directory the directory to use for caching
         * @return object reference
         */
        public function setDirectory($directory)
        {
            TypeValidator::IsString($directory);

            $this->directory = rtrim(realpath($directory), '\\/') . DIRECTORY_SEPARATOR;

            return $this;
        }

        /**
         * Class constructor.
         * Initializes the class properties.
         * @return void
         */
        public function __construct()
        {
            $this->reset();
        }

        /**
         * Resets the class properties.
         * @return object reference
         */
        public function reset()
        {
            $this->directory     = null;
            $this->filePrefix    = 'cache_';

            return $this;
        }

        public function getFileName($identifier)
        {
            TypeValidator::IsString($identifier);

            return $this->getDirectory() . $this->getFilePrefix() . str_replace(' ', '_', $identifier);
        }

        /**
         * Returns the cached content from the matching dentifier.
         * Removes the cached content if it has expired.
         * @param string $identifier the identifier to retrieve the content from
         * @return mixed the cached content or false if the content is not available or has expired
         */
        public function get($identifier)
        {
            TypeValidator::IsString($identifier);

            if (! file_exists(($fileName = $this->getFileName($identifier))))
            {
                return false;
            }

            $FileObject = $this->getFileObject();

            $expirationDate = $FileObject->setLocation($fileName)->setMode('r')->read(self::LIFETIME_BYTES);
            if(strtotime($expirationDate) < time())
            {
                $FileObject->close();
                $this->delete($identifier);

                return false;
            }

            $cachedContent = $FileObject->read(filesize($fileName) - self::LIFETIME_BYTES);
            $FileObject->close();

            return unserialize($cachedContent);
        }

        /**
         * Sets the content holded by the given identifier.
         * If the identifer already exists the content will be replaced.
         * If the lifetime is zero the content will be cached for one year.
         * @param string $identifier the identifier which should hold the content
         * @param mixed $content the content which should be cached
         * @param integer $lifetime the lifetime in seconds of the cached content
         * @return boolean true if the content could be saved
         */
        public function set($identifier, $content, $lifetime = 60)
        {
            TypeValidator::IsString($identifier);
            TypeValidator::IsInteger($lifetime);

            if ($lifetime == 0)
            {
                $lifetime = (365 * 24 * 60 * 60);
            }

            $this->getFileObject()->setLocation($this->getFileName($identifier))
                                  ->setMode('w')
                                  ->write(date(self::LIFETIME_FORMAT, (time()+ $lifetime)))
                                  ->write(serialize($content))
                                  ->close();
            return true;
        }

        /**
         * Deletes the identifier and cached content.
         * @param string $identifier the identifer to remove
         * @return boolean true if the file did be deleted otherwise false
         */
        public function delete($identifier)
        {
            TypeValidator::IsString($identifier);

            if (file_exists(($fileName = $this->getFileName($identifier))))
            {
                return unlink($fileName);
            }

            return false;
        }

        /**
         * Flushes the cached values by unlinking any file which has the file prefix.
         * @return integer the number of files which have been unlinked
         */
        public function flush()
        {
            $filePrefix = $this->getFilePrefix();

            $DirectoryIerator = new \DirectoryIterator(($directory = $this->getDirectory()));

            $unlinkedCounter = 0;

            foreach ($DirectoryIerator as $FileInfo)
            {
                if
                (
                    $FileInfo->isFile()
                    &&
                    (substr(($fileName = $FileInfo->getFilename()), 0, strlen($filePrefix)) == $filePrefix)
                )
                {
                    unlink($directory . $fileName);
                    $unlinkedCounter++;
                }
            }

            return $unlinkedCounter;
        }

    }

?>