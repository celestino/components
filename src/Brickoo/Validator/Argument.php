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

    namespace Brickoo\Validator;

    /**
     * Argument
     * Validates an argument by expectation and
     * throws an \InvalidArgumentException if the validation fails.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class Argument {

        /**
         * Checks if the argument is a string and not empty, accepts empty strings.
         * @param string $arguments the arguments to validate
         * @throws \InvalidArgumentException if the validation fails
         * @return boolean check result
         */
        public static function IsString($argument) {
            if (! is_string($argument)) {
                return self::ThrowInvalidArgumentException($argument, "The argument must be of type string.");
            }

            return true;
        }

        /**
         * Checks if the arguments is an integer.
         * @param float $argument the argument to check
         * @throws \InvalidArgumentException if the validation fails
         * @return boolean check result
         */
        public static function IsInteger($argument) {
            if (! is_int($argument)) {
                return self::ThrowInvalidArgumentException($argument, "The argument must be of type integer.");
            }

            return true;
        }

        /**
         * Checks if the argument is a string or a integer, accepts empty values.
         * @param string $argument the argument to validate
         * @throws \InvalidArgumentException if the validation fails
         * @return boolean check result
         */
        public static function IsStringOrInteger($argument) {
            if ((! is_string($argument)) && (! is_int($argument))) {
                return self::ThrowInvalidArgumentException($argument, "The argument must be of type integer or string.");
            }

            return true;
        }

        /**
         * Checks if the arguments is a float.
         * @param float $argument the argument to check
         * @throws \InvalidArgumentException if the validation fails
         * @return boolean check result
         */
        public static function IsFloat($argument) {
            if (! is_float($argument)) {
                return self::ThrowInvalidArgumentException($argument, "The argument must be of type float.");
            }

            return true;
        }

        /**
         * Checks if the argument is a boolean.
         * @param string $argument the argument to validate
         * @throws \InvalidArgumentException if the validation fails
         * @return boolean check result
         */
        public static function IsBoolean($argument) {
            if (! is_bool($argument)) {
                return self::ThrowInvalidArgumentException($argument, "The argument must be of type boolean.");
            }

            return true;
        }

        /**
         * Checks if the argument is not empty.
         * @param string $argument the argument to validate
         * @throws \InvalidArgumentException if the validation fails
         * @return boolean check result
         */
        public static function IsNotEmpty($argument) {
            if (empty($argument)) {
                return self::ThrowInvalidArgumentException($argument, "The argument must not be empty.");
            }

            return true;
        }

        /**
         * Checks if a function is available by its name.
         * @param string $argument the argument to validate
         * @throws \InvalidArgumentException if the validation fails
         * @return boolean check result
         */
        public static function IsFunctionAvailable($argument) {
            if (! function_exists($argument)) {
                return self::ThrowInvalidArgumentException($argument, "The argument must be an available function.");
            }

            return true;
        }

        /**
         * Checks if a the argument is traversable.
         * @param string $argument the argument to validate
         * @throws \InvalidArgumentException if the validation fails
         * @return boolean check result
         */
        public static function IsTraversable($argument) {
            if ((! is_array($argument)) && (! $argument instanceof \Traversable)) {
                return self::ThrowInvalidArgumentException($argument, "The argument must be traversable.");
            }

            return true;
        }

        /**
         * Checks if a argument is callable.
         * @param mixed $argument the argument to validate
         * @throws \InvalidArgumentException if the validation fails
         * @return boolean check result
         */
        public static function isCallable($argument) {
            if (! is_callable($argument)) {
                return self::ThrowInvalidArgumentException($argument, "The argument must be callable.");
            }

            return true;
        }

        /**
         * Checks if a argument is an object.
         * @param mixed $argument the argument to validate
         * @throws \InvalidArgumentException if the validation fails
         * @return boolean check result
         */
        public static function IsObject($argument){
            if (! is_object($argument)) {
                return self::ThrowInvalidArgumentException($argument, "The argument must be an object.");
            }

            return true;
        }

        /**
         * Throws an \invalidArgumentException describing the argument and adding a helpful error message.
         * @param mixed $argument the arguments which is invalid
         * @param string $errorMessage the error message to attach
         * @throws \InvalidArgumentException
         * @return void
         */
        public static function ThrowInvalidArgumentException($argument, $errorMessage) {
            throw new \InvalidArgumentException(
                sprintf(
                    "Unexpected argument %s. %s",
                    self::GetArgumentStringRepresentation($argument),
                    $errorMessage
                )
            );
        }

        /**
         * Returns the argument string representation.
         * @param mixed $argument the argument to return the representation
         * @return string the argument representation
         */
        private static function GetArgumentStringRepresentation($argument) {
            switch (gettype($argument)) {
                case "object":
                    $representation = sprintf("[object #%s] %s", spl_object_hash($argument), get_class($argument));
                    break;
                default:
                    $representation = sprintf(
                        "[%s] %s",
                        gettype($argument),
                        str_replace(array("\r", "\n", " "), "", var_export($argument, true))
                    );
            }
            return $representation;
        }

    }