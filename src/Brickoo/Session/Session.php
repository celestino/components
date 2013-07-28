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

    namespace Brickoo\Session;

    use Brickoo\Validator\Argument;

    /**
     * Session
     *
     * Object wrapper for the PHP session functions.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class Session implements Interfaces\Session {

        /** @var boolean */
        protected static $SessionStarted;

        /** {@inheritDoc} */
        public function registerHandler($Handler) {
            Argument::IsObject($Handler);

            $this->checkSessionStart();

            if ((! $Handler instanceOf \Brickoo\Session\Interfaces\Handler)
                && ((! interface_exists("\\SessionHandler", false)) || (! $Handler instanceof \SessionHandler))
            ){
                throw new \InvalidArgumentException(
                    sprintf("The class %s does not implement a valid interface.", get_class($Handler))
                );
            }

            session_set_save_handler(
                array($Handler, 'open'),
                array($Handler, 'close'),
                array($Handler, 'read'),
                array($Handler, 'write'),
                array($Handler, 'destroy'),
                array($Handler, 'gc')
            );
            return $this;
        }

        /** {@inheritDoc} */
        public function getId() {
            return session_id();
        }

        /** {@inheritDoc} */
        public function setId($identifier) {
            Argument::IsString($identifier);

            $this->checkSessionStart();

            return session_id($identifier);
        }

        /** {@inheritDoc} */
        public function regenerateId() {
            session_regenerate_id(false);
            return $this;
        }

        /** {@inheritDoc} */
        public function getName() {
            return session_name();
        }

        /** {@inheritDoc} */
        public function setName($name) {
            Argument::IsString($name);

            $this->checkSessionStart();

            return session_name($name);
        }

        /** {@inheritDoc} */
        public function getCookieParams() {
            return session_get_cookie_params();
        }

        /** {@inheritDoc} */
        public function setCookieParams($lifetime, $path, $domain, $secure = false, $httponly = false) {
            Argument::IsInteger($lifetime);
            Argument::IsString($path);
            Argument::IsString($domain);
            Argument::IsBoolean($secure);
            Argument::IsBoolean($httponly);

            $this->checkSessionStart();

            session_set_cookie_params($lifetime, $path, $domain, $secure, $httponly);
            return $this;
        }

        /** {@inheritDoc} */
        public function getCacheLimiter() {
            return session_cache_limiter();
        }

        /** {@inheritDoc} */
        public function setCacheLimiter($limiter) {
            Argument::IsString($limiter);
            session_cache_limiter($limiter);
            return $this;
        }

        /** {@inheritDoc} */
        public function start() {
            $this->checkSessionStart();

            if (session_start()) {
                self::$SessionStarted = true;
            }
            return $this;
        }

        /** {@inheritDoc} */
        public function hasStarted() {
            return self::$SessionStarted === true;
        }

        /** {@inheritDoc} */
        public function stop() {
            session_write_close();
            self::$SessionStarted = null;
            return $this;
        }

        /** {@inheritDoc} */
        public function destroy($callback = null) {
            if ($this->isCookieUsed()) {
                $callback = is_callable($callback) ? $callback : "setcookie";
                $params = session_get_cookie_params();
                call_user_func_array($callback, array(
                    session_name(), "", time() - (365 * 24 *60 * 60),
                    $params["path"], $params["domain"],
                    $params["secure"], $params["httponly"]
                ));
            }
            $_SESSION = array();
            session_destroy();

            return $this;
        }

        /**
         * Checks if the session has been started.
         * If the session has been started something
         * is going wrong, throws an exception
         * @throws Exceptions\SessionAlreadyStarted
         * @return void
         */
        private function checkSessionStart() {
            if ($this->hasStarted()) {
                throw new Exceptions\SessionAlreadyStarted();
            }
        }

        /**
         * Checks if session cookies are used.
         * @return boolean check result
         */
        private function isCookieUsed() {
            return (boolean)ini_get("session.use_cookies");
        }

    }