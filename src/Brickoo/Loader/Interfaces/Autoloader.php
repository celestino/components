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

    namespace Brickoo\Loader\Interfaces;

    /**
     * Autoloader
     *
     * Defines an autoloader for classes auto loading.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    interface Autoloader {

        /**
         * Register the autoloader.
         * @throws \Brickoo\Loader\Exceptions\DuplicateAutoloaderRegistration if the autoloader is already registered
         * @return \Brickoo\Loader\Interfaces\Autoloader
         */
        public function register();

        /**
         * Unregister the autoloader.
         * @throws \Brickoo\Loader\Exceptions\AutoloaderNotRegistered if the autoloader did not be registered
         * @return \Brickoo\Loader\Interfaces\Autoloader
         */
        public function unregister();

        /**
         * Loads the requested class.
         * Commomly this is the autoload callback function registered.
         * @param string $className the class to load
         * @throws \InvalidArgumentException if an argument is not valid
         * @throws \Brickoo\Loader\Exceptions\FileDoesNotExist throws an exception if the file does not exists
         * @return boolean true on success false on failure
         */
        public function load($className);

    }