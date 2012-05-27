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

    namespace Brickoo\Http\Session\Handler;

    use Brickoo\Cache,
        Brickoo\Validator\TypeValidator;

    /**
     * CacheHandler
     *
     * Handles the session operations with an Manager instance.
     * The dedault Manager used has a File injected which will save the sessions
     * to the default PHP temporary directory.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class CacheHandler implements Interfaces\SessionHandler {

        /**
         * Holds the cache prefix for the session.
         * @var string
         */
        const SESSION_CACHE_PREFIX = 'php_session_';

        /**
         * Holds the class dependencies.
         * @var array
         */
        protected $dependencies;

        /**
         * Returns the dependency holded, created or overwritten.
         * @param string $name the name of the dependency
         * @param string $interface the interface which has to be implemented by the dependency
         * @param callback $callback the callback to create a new dependency
         * @param object $Dependency the dependecy to inject
         * @return object Request if overwritten otherwise the dependency
         */
        protected function getDependency($name, $interface, $callback, $Dependency = null) {
            if ($Dependency instanceof $interface) {
                $this->dependencies[$name] = $Dependency;
                return $this;
            }
            elseif ((! isset($this->dependencies[$name])) || (! $this->dependencies[$name] instanceof $interface)) {
                $this->dependencies[$name] = call_user_func($callback, $this);
            }
            return $this->dependencies[$name];
        }

        /**
         * Lazy initialization of the Manager dependency with the default File.
         * @param \Brickoo\Cache\Interfaces\Manager $Manager the Manager depedency
         * @return \Brickoo\Cache\Interfaces\Manager
         */
        public function Manager(\Brickoo\Cache\Interfaces\Manager $Manager = null) {
            return $this->getDependency(
                'Manager',
                '\Brickoo\Cache\Interfaces\Manager',
                function(){return new Cache\Manager(new Cache\Provider\File());},
                $Manager
            );
        }

        /**
         * Holds the lifetime of the cached session.
         * @var integer
         */
        protected $lifetime;

        /**
         * Sets the session lifetime in seconds.
         * @param integer $lifetime the lifetime of the session
         * @return \Brickoo\Http\Session\Handler\CacheHandler
         */
        public function setLifetime($lifetime) {
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
        public function open($savePath, $sessionName) {
            $this->Manager()->disableLocalCache();

            return true;
        }

        /**
         * Closes the session.
         * This method is called when the session has been closed at the end of the application.
         * @return boolean true
         */
        public function close() {
            return true;
        }

        /**
         * Reads the session content and returns the available session serialized content.
         * This method is called when the session is started.
         * @param string $identifier the session identifier from where the content should be returned.
         * @return string the session content
         */
        public function read($identifier) {
            return $this->Manager()->get(self::SESSION_CACHE_PREFIX . $identifier);
        }

        /**
         * Writes the session.
         * This method is called when the session content must be written / cached.
         * @param string $identifier the session identifier holding the content
         * @param mixed $data the data to save
         * @return boolean true
         */
        public function write($identifier, $data) {
            $this->Manager()->set(self::SESSION_CACHE_PREFIX . $identifier, $data, $this->lifetime);
            return true;
        }

        /**
         * Destroys the session identifier.
         * This method is called when a session identifier must be destroyed.
         * The session should be removed to prevent session fixation.
         * @param string $identifier the session identifier to remove
         * @return boolean true
         */
        public function destroy($identifier) {
            $this->Manager()->delete(self::SESSION_CACHE_PREFIX . $identifier);
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
        public function gc($maxlifetime) {
            return true;
        }

    }