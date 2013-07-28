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

    namespace Brickoo\Session\Interfaces;

    /**
     * Session
     *
     * Describes a session object.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    interface Session {

        /**
         * Registers the session handler to PHP.
         * The valid handlers must implement the \Brickoo\Session\Interfaces\Handler
         * or the new in PHP 5.4 introduced \SessionHandler interface.
         * @param object $Handler the session handler
         * @throws \InvalidArgumentException if the argument is not valid
         * @return \Brickoo\Session\Interfaces\Session
         */
        public function registerHandler($Handler);

        /**
         * Returns the current session identifier.
         * @return string the session identifier
         */
        public function getId();

        /**
         * Set the session identifier.
         * @param string $identifier the session identifier
         * @throws \Brickoo\Session\Exceptions\SessionAlreadyStarted
         * @throws \InvalidArgumentException if the argument is not valid
         * @return string the previously used session identifier
         */
        public function setId($identifier);

        /**
         * Regenerates the session id.
         * The values of the current session should be keeped.
         * @return \Brickoo\Session\Interfaces\Session
         */
        public function regenerateId();

        /**
         * Returns the current session name.
         * @return string the current session name
         */
        public function getName();

        /**
         * Sets the session name.
         * @param string $name the session name
         * @throws \Brickoo\Session\Exceptions\SessionAlreadyStarted
         * @throws \InvalidArgumentException if the argument is not valid
         * @return string the previously used session name
         */
        public function setName($name);

        /**
         * Returns the current session cookie parameters.
         * @return array the cookie parameters
         */
        public function getCookieParams();

        /**
         * Sets the session cookie parameters.
         * @param integer $lifetime the cookie/session lifetime
         * @param string $path the request path to listen to
         * @param string $domain the domain to listen to
         * @param boolean $secure only should be sent while on https mode
         * @param boolean $httponly restriction to the http protocol
         * @throws \Brickoo\Session\Exceptions\SessionAlreadyStarted
         * @throws \InvalidArgumentException if an argument is not valid
         * @return \Brickoo\Session\Interfaces\Session
         */
        public function setCookieParams($lifetime, $path, $domain, $secure, $httponly);

        /**
         * Returns the current session cache limiter.
         * @return string the current used session cache limiter
         */
        public function getCacheLimiter();

        /**
         * Sets the session cache limiter.
         * @see session_cache_limiter() for possible values
         * @param string $limiter the cache limiter
         * @throws \InvalidArgumentException if the argument is not valid
         * @return \Brickoo\Session\Interfaces\Session
         */
        public function setCacheLimiter($limiter);

        /**
         * Starts the session.
         * @throws \Brickoo\Session\Exceptions\SessionAlreadyStarted
         * @return \Brickoo\Session\Interfaces\Session
         */
        public function start();

        /**
         * Checks if the session has already been started.
         * @return boolean check result
         */
        public function hasStarted();

        /**
         * Stops the session by writting the current session values.
         * @return \Brickoo\Session\Interfaces\Session
         */
        public function stop();

        /**
         * Destroys the current session.
         * To prevent unexpected behaviours
         * the global $_SESSION variable should be cleared,
         * also the cookie (if used) should be expired.
         * This method is commonly used when logging-out an user.
         * @return \Brickoo\Session\Interfaces\Session
         */
        public function destroy();

    }