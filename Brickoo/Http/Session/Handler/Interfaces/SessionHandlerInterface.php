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

    namespace Brickoo\Library\Http\Session\Handler\Interfaces;

    /**
     * SessionHandlerInterface
     *
     * Describes the methods implemented by this interface.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    interface SessionHandlerInterface
    {

        /**
        * Sets the session lifetime in seconds.
        * @param integer $lifetime the lifetime of the session
        * @return object \Brickoo\Library\Http\Session\Interfaces\SessionHandlerInterface
        */
        public function setLifetime($lifetime);

        /**
         * Opens the session.
         * This method is called when the session is started.
         * @param string $savePath the save path of the session
         * @param string $sessionName the session name
         * @return boolean true
         */
        public function open($savePath, $sessionName);

        /**
         * Closes the session.
         * This method is called when the session has been closed at the end of the application.
         * @return boolean true
         */
        public function close();

        /**
         * Reads the session content and returns the available session serialized content.
         * @param string $identifier the session identifier from where the content should be returned.
         * @return string the session content
         */
        public function read($identifier);

        /**
         * Writes the session.
         * This method is called when the session content must be written / cached.
         * @param string $identifier the session identifier holding the content
         * @param mixed $data the data to save
         * @return boolean true
         */
        public function write($identifier, $data);

        /**
         * Destroys the session identifier.
         * This method is called when a session identifier must be destroyed.
         * The session should be removed to prevent session fixation.
         * @param string $identifier the session identifier to remove
         * @return boolean true
         */
        public function destroy($identifier);

        /**
         * Garbage collection for the available sessions.
         * This method is called when the expired session should be removed.
         * @param integer $maxlifetime the max lifetime of the sessions
         * @return boolean true
         */
        public function gc($maxlifetime);

    }