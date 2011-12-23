<?php

    /*
     * Copyright (c) 2008-2011, Celestino Diaz Teran <celestino@users.sourceforge.net>.
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

    namespace Brickoo\Library\Core;

    use Brickoo\Library\Storage;
    use Brickoo\Library\Core\Exceptions;

    /**
     * Brickoo framework main class.
     * Used to have an global Registry which is part managed by the framework.
     *
     * Holds an object of the \Brickoo\Library\Storage\Registry.
     * @author Celestino Diaz Teran <celestino@users.sourceforge.net>
     * @version $Id$
     */

    class Brickoo
    {

        /**
         * Holds an object of the Registry.
         * @var Brickoo\Library\Storage\Interfaces\RegistryInterface object
         */
        protected static $Registry;

        /**
         * Lazy initialization of the Registry object.
         * Returns the holded Registry instance.
         * @return Brickoo\Library\Storage\Interfaces\RegistryInterface object reference
         */
        public function getRegistry()
        {
            if (self::$Registry === null)
            {
                $this->injectRegistry(new Storage\Registry());
            }

            return self::$Registry;
        }

        /**
         * Injects the Registry object to use for storing entries.
         * @param Brickoo\Library\Storage\Interfaces\RegistryInterface $Registry
         * @throws DependencyOverrideException if trying to override dependency
         * @return object reference
         */
        public function injectRegistry(\Brickoo\Library\Storage\Interfaces\RegistryInterface $Registry = null)
        {
            if (self::$Registry !== null)
            {
                throw new Exceptions\DependencyOverrideException('RegistryInterface');
            }

            self::$Registry = new Storage\Registry();

            return $this;
        }

    }

?>