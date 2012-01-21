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

    namespace Brickoo\Library\Session;

    use Brickoo\Library\Session\Interfaces;
    use Brickoo\Library\Validator\TypeValidator;

    /**
     * SessionNamespace
     *
     * Provides a session object based on namespaces which should prevent conflicts.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class SessionNamespace implements Interfaces\SessionNamespaceInterface
    {

        /**
         * Holds the session namespace using.
         * @var string
         */
        protected $sessionNamespace;

        /**
         * Class constructor.
         * Sets the sessionNamespace to use.
         * @param string $sessionNamespace the namspace to use
         */
        public function __construct($sessionNamespace)
        {
            TypeValidator::MatchesRegex('~^[\w]+$~', $sessionNamespace);

            $this->sessionNamespace = $sessionNamespace;
        }

        /**
        * Checks if the session property is available.
        * @param string $property the property to check in the session
        * @return boolean check result
        */
        public function has($property)
        {
            TypeValidator::IsString($property);

            return array_key_exists($this->sessionNamespace . '.' . $property, $_SESSION);
        }

        /**
         * Returns the session property holded content or the default value.
         * @param string $property the session property to retrieve the content from
         * @param mixed $defaultValue the default value if the property des not exist
         * @return mixed the property holded content or the default value if the property does not exist
         */
        public function get($property, $defaultValue = null)
        {
            TypeValidator::IsString($property);

            if (! $this->has($property))
            {
                return $defaultValue;
            }

            return $_SESSION[$this->sessionNamespace . '.' . $property];
        }

        /**
         * Sets the session property and assigns the content to it.
         * @param string $property the property to assign the content to
         * @param mixed $content the content to store
         * @return \Brickoo\Library\Session\Session
         */
        public function set($property, $content)
        {
            TypeValidator::IsString($property);

            $_SESSION[$this->sessionNamespace . '.' . $property] = $content;

            return $this;
        }

        /**
         * Removes the session property if available.
         * @param string $property the property to remove
         * @return \Brickoo\Library\Session\Session
         */
        public function remove($property)
        {
            TypeValidator::IsString($property);

            if ($this->has($property))
            {
                unset($_SESSION[$this->sessionNamespace . '.' . $property]);
            }

            return $this;
        }

        /**
         * Magic function to retrieve the content of a session property.
         * @param string $property the property to retrieve the content from
         * @return mixed the session property content or boolean false if the property is not available
         */
        public function __get($property)
        {
            return $this->get($property, false);
        }

        /**
         * Magic function to assign the content to a session property.
         * @param string $property the property to assign the content to
         * @return void
         */
        public function __set($property, $content)
        {
            $this->set($property, $content);
        }

        /**
         * Magic function to unset a session property.
         * @param string $property the property to unset
         * @return void
         */
        public function __unset($property)
        {
            $this->remove($property);
        }

        /**
         * Magic function to check if a session property is set.
         * @param string $property the property to check
         * @return boolean check result
         */
        public function __isset($property)
        {
            return $this->has($property);
        }

    }

?>