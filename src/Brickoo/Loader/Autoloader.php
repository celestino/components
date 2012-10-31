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

    namespace Brickoo\Loader;

    require_once 'Interfaces/Autoloader.php';

    /**
     * Autoloader
     *
     * Abstract implementation of an autoloader.
     * \Brickoo\Loader\Autoloader::load method has to be overriden,
     * since it is just a dummy implementation.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    abstract class Autoloader implements Interfaces\Autoloader {


        /** @var boolean */
        private $isRegistered;

        /** @var boolean*/
        private $prependAutoloader;

        /**
         * Class constructor.
         * @param boolean $prepend flag to prepend or append to the PHP autoloader list
         * @return void
         */
        public function __construct($prepend = true) {
            $this->isRegistered = false;
            $this->prependAutoloader = (boolean)$prepend;
        }

        /** {@inheritDoc} */
        public function register() {
            if ($this->isRegistered) {
                require_once 'Exceptions/DuplicateAutoloaderRegistration.php';
                throw new Exceptions\DuplicateAutoloaderRegistration();
            }

            spl_autoload_register(array($this, 'load'), true, $this->prependAutoloader);
            $this->isRegistered = true;

            return $this;
        }

        /** {@inheritDoc} */
        public function unregister() {
            if (! $this->isRegistered) {
                require_once 'Exceptions/AutoloaderNotRegistered.php';
                throw new Exceptions\AutoloaderNotRegistered();
            }

            spl_autoload_unregister(array($this, 'load'));
            $this->isRegistered = false;

            return $this;
        }

        /** {@inheritDoc} */
        public function load($className) {}

    }