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

    use Brickoo\Library\Validator\TypeValidator;

    /**
     * BrickooObject
     *
     * Brickoo Object for extending classes with need global Registry access.
     * @author Celestino Diaz Teran <celestino@users.sourceforge.net>
     * @version $Id: $
     */

    class BrickooObject
    {

        /**
        * Holds the Brickoo object.
        * @var object
        */
        protected $Brickoo;

        /**
        * Holds the Brickoo substitutor object.
        * @var object implementing BrickooInterface
        */
        protected $BrickooSubstitutor;

        /**
        * Class cosntructor.
        * If Object is given as parameter, the object will be used as reference
        * otherwise the static Brickoo class will be used.
        * @return void
        */
        public function __construct(Interfaces\BrickooSubstitutor $BrickooSubstitutor = null)
        {
            if($BrickooSubstitutor !== null)
            {
                $this->BrickooSubstitutor = $BrickooSubstitutor;
            }
            else
            {
                $this->Brickoo = new Brickoo();
            }
        }

        /**
         * Returns the registered entry.
         * @param string|integer $identifier the identifier registered.
         * @return mixed the registered identifier value
         */
        public function getRegistryEntry($identifier)
        {
            if($this->BrickooSubstitutor !== null)
            {
                return $this->BrickooSubstitutor->getRegistryEntry($identifier);
            }

            return $this->Brickoo->getRegistryEntry($identifier);
        }

        /**
        * Registers an identifier to the registry.
        * @param string|integer $identifier the identifier to register
        * @param mixed $value the value to assign
        * @return boolean success
        */
        public function addRegistryEntry($identifier, $value = null)
        {
            if($this->BrickooSubstitutor !== null)
            {
                return $this->BrickooSubstitutor->addRegistryEntry($identifier, $value);
            }

            return $this->Brickoo->addRegistryEntry($identifier, $value);
        }

    }

?>