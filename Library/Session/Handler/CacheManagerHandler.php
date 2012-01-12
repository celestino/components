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

    namespace Brickoo\Library\Session\Handler;

    use Brickoo\Library\Core;
    use Brickoo\Library\Cache;
    use Brickoo\Library\Session;
    use Brickoo\Library\Validator\TypeValidator;

    /**
     * CacheManagerHandler
     *
     * Handles the session operations with an CacheManager instance.
     * The dedault CacheManager used has a FileProvider injected which will save the sessions
     * to the default PHP temporary directory.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class CacheManagerHandler implements Session\Interfaces\SessionHandlerInterface
    {

        /**
         * Holds the cache prefix for the session.
         * @var string
         */
        const SESSION_CACHE_PREFIX = 'php_session_';

        /**
         * Holds an instance of a CacheManager implementing the Cache\Interfaces\CacheManagerInterface.
         * @var object
         */
        protected $CacheManager;

        /**
         * Lazy initialization of the CacheManager dependency with a FileProvider.
         * Returns the CacheManager dependency.
         * @return \Brickoo\Library\Cache\Interfaces\CacheManagerInterface
         */
        public function getCacheManager()
        {
            if (! $this->CacheManager instanceof Cache\Interfaces\CacheManagerInterface)
            {
                $this->CacheManager = new Cache\CacheManager(new Cache\Provider\FileProvider());
            }

            return $this->CacheManager;
        }

        /**
         * Injects the CacheManager dependency implementing the Cache\Interfaces\CacheManagerInterface.
         * @param \Brickoo\Library\Cache\Interfaces\CacheManagerInterface $CacheManager the CacheManager depedency
         * @throws Core\Exceptions\DependencyOverwriteException if trying to overwrite the dependency
         * @return \Brickoo\Library\Session\Handler\CacheManagerHandler
         */
        public function injectCacheManager(\Brickoo\Library\Cache\Interfaces\CacheManagerInterface $CacheManager)
        {
            if ($this->CacheManager !== null)
            {
                throw new Core\Exceptions\DependencyOverwriteException('CacheManagerInterface');
            }

            $this->CacheManager = $CacheManager;

            return $this;
        }

        /**
         * Holds the lifetime of the cached session.
         * @var integer
         */
        protected $lifetime;

        /**
         * Sets the session lifetime in seconds.
         * @param integer $lifetime the lifetime of the session
         * @return \Brickoo\Library\Session\Handler\CacheManagerHandler
         */
        public function setLifetime($lifetime)
        {
            TypeValidator::IsInteger($lifetime);

            $this->lifetime = $lifetime;

            return $this;
        }

        /**
         * Opens the session.
         * This method is called when the session is started.
         * The only meaning for this handler is to diasable the local cache since it
         * will not be used and would just cost memory space.
         * @param string $savePath the save path of the session
         * @param string $sessionName the session name
         * @return boolean true
         */
        public function open($savePath, $sessionName)
        {
            $this->getCacheManager()->disableLocalCache();

            return true;
        }

        /**
         * Closes the session.
         * This method is called when the session has been closed at the end of the application.
         * @return boolean true
         */
        public function close()
        {
            return true;
        }

        /**
         * Reads the session content and returns the available session serialized content.
         * This method is called when the session is started.
         * @param string $identifier the session identifier from where the content should be returned.
         * @return string the session content
         */
        public function read($identifier)
        {
            return $this->getCacheManager()->get(self::SESSION_CACHE_PREFIX . $identifier);
        }

        /**
         * Writes the session.
         * This method is called when the session content must be written / cached.
         * @param string $identifier the session identifier holding the content
         * @param mixed $data the data to save
         * @return boolean true
         */
        public function write($identifier, $data)
        {
            $this->getCacheManager()->set(self::SESSION_CACHE_PREFIX . $identifier, $data, $this->lifetime);
            return true;
        }

        /**
         * Destroys the session identifier.
         * This method is called when a session identifier must be destroyed.
         * The session should be removed to prevent session fixation.
         * @param string $identifier the session identifier to remove
         * @return boolean true
         */
        public function destroy($identifier)
        {
            $this->getCacheManager()->delete(self::SESSION_CACHE_PREFIX . $identifier);
            return true;
        }

        /**
         * Garbage collection for the available sessions.
         * This method is called when the expired session should be removed.
         * The CacheMager odes not use garbe collection since the files will be automaticly removed
         * if they are expired and the memory based cache provider will handle this by their own.
         * @param integer $maxlifetime the max lifetime of the sessions
         * @return boolean true
         */
        public function gc($maxlifetime)
        {
            return true;
        }

    }

?>