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

    use Brickoo\Library\Storage\Registry;

    /**
     * Brickoo framework main class.
     * Used to have an global Registry which is part managed by the framework.
     *
     * Holds an object of the \Brickoo\Library\Storage\Registry.
     * @author Celestino Diaz Teran <celestino@users.sourceforge.net>
     * @version $Id: $
     */

    class Brickoo
    {

        /**
         * Holds an object of the Registry.
         * @var \Brickoo\Library\Storage\Registry object
         */
        protected static $Registry;

        /**
         * Returns the holded Registry object.
         * @return \Brickoo\Library\Storage\Registry object
         */
        public function getRegistry()
        {
            if (self::$Registry === null)
            {
                $this->setRegistry();
            }

            return self::$Registry;
        }

        /**
         * Sets the Registry object to use for storing values.
         * @param \Brickoo\Library\Storage\Registry $Registry
         * @return object Registry reference
         */
        public function setRegistry(Registry $Registry = null)
        {
            if (self::$Registry !== null)
            {
                throw new \LogicException('Registry instance already assigned.', E_ERROR);
            }

            if
            (
               ($Registry !== null)
               &&
               (self::$Registry === null)
            )
            {
               self::$Registry = $Registry;
            }
            else
            {
               if (self::$Registry === null)
               {
                   self::$Registry = new Registry();
               }
            }

            return self::$Registry;


        }

        /**
         * Returns the registered entry.
         * @see \Brickoo\Library\Storage\Registry::getRegistered
         * @param string|integer $identifier the identifier registered
         * @return mixed the registered identifier value
         */
        public function getRegistryEntry($identifier)
        {
            return $this->getRegistry()->getRegistered($identifier);
        }

        /**
         * Registers an identifier to the registry.
         * @see \Brickoo\Library\Storage\Registry::register
         * @param string|integer $identifier the identifier to register
         * @param mixed $value the value to assign
         * @return boolean success
         */
        public function addRegistryEntry($identifier, $value = null)
        {
            return $this->getRegistry()->register($identifier, $value);
        }

    }

?>