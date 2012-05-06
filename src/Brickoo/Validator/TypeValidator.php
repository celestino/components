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

    namespace Brickoo\Validator;

    /**
     * ArgumentValidator
     *
     * Used for validating passed arguments to methods.
     * Throws an InvalidArgumentException if the validation fails.
     * @see http://php.net/manual/de/class.invalidargumentexception.php
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class TypeValidator {

        /**
         * Exception message with placeholders.
         * @var string
         */
        const EXCEPTION_MESSAGE = 'Validation with argument `%s` on method `%s` failed.';

        public static function ThrowInvalidArgumentException($argument, $method) {
            switch (gettype($argument)) {
                case "boolean":
                    $argument = ($argument ? 'true' : 'false');
                    break;
                case "array":
                    $argument = str_replace(array("\r", "\n", " "), "", var_export($argument, true));
                    break;
                case "object":
                    $argument = "[object] ". get_class($argument);
                    break;
                case "NULL":
                    $argument = "null";
                    break;
                default:
                    $argument = serialize($argument);
            }

            throw new \InvalidArgumentException(
                sprintf(self::EXCEPTION_MESSAGE, $argument, $method)
            );
        }



        /**
         * Checks if the argument is a string and not empty, accepts empty strings.
         * @param string $arguments the arguments to validate
         * @throws \InvalidArgumentException if the valiadtion fails
         * @return boolean check result
         */
        public static function IsString($argument) {
            if (! is_string($argument)) {
                return self::ThrowInvalidArgumentException($argument, __METHOD__);
            }

            return true;
        }

        /**
         * Checks if the argument is a string and not empty.
         * @param string $arguments the arguments to validate
         * @throws \InvalidArgumentException if the valiadtion fails
         * @return boolean check result
         */
        public static function IsStringAndNotEmpty($argument) {
           if ((! is_string($argument)) || (! $argument = trim($argument)) || empty($argument)) {
               return self::ThrowInvalidArgumentException($argument, __METHOD__);
           }

           return true;
        }

        /**
         * Checks if the arguments is an integer.
         * @param float $argument the argument to check
         * @throws \InvalidArgumentException if the valiadtion fails
         * @return boolean check result
         */
        public static function IsInteger($argument) {
            if (! is_int($argument)) {
                return self::ThrowInvalidArgumentException($argument, __METHOD__);
            }

            return true;
        }

        /**
         * Checks if the argument is a integer and not zero.
         * @param integer $argument the argument to validate
         * @throws \InvalidArgumentException if the valiadtion fails
         * @return boolean check result
         */
        public static function IsIntegerAndNotZero($argument) {
            if ((! is_int($argument)) || $argument === 0) {
                return self::ThrowInvalidArgumentException($argument, __METHOD__);
            }

            return true;
        }

        /**
         * Checks if the argument is a string or a integer, accepts empty values.
         * @param string $argument the argument to validate
         * @throws \InvalidArgumentException if the valiadtion fails
         * @return boolean check result
         */
        public static function IsStringOrInteger($argument) {
            if ((! is_string($argument)) && (! is_int($argument))) {
                return self::ThrowInvalidArgumentException($argument, __METHOD__);
            }

            return true;
        }

        /**
         * Checks if the arguments is a float.
         * @param float $argument the argument to check
         * @throws \InvalidArgumentException if the valiadtion fails
         * @return boolean check result
         */
        public static function IsFloat($argument) {
            if (! is_float($argument)) {
                return self::ThrowInvalidArgumentException($argument, __METHOD__);
            }

            return true;
        }

        /**
         * Checks if the argument is an array and not empty.
         * @param string $argument the argument to validate
         * @throws \InvalidArgumentException if the valiadtion fails
         * @return boolean check result
         */
        public static function IsArrayAndNotEmpty($argument) {
            if ((! is_array($argument)) || empty($argument)) {
                return self::ThrowInvalidArgumentException($argument, __METHOD__);
            }

            return true;
        }

        /**
         * Checks if the array contains only string values.
         * @param array $argument the argument to validate
         * @throws \InvalidArgumentException if the valiadtion fails
         * @return boolean check result
         */
        public static function ArrayContainsStrings($argument) {
            if
            (!
                (
                    is_array($argument) &&
                    ($filtered = array_filter($argument, 'is_string')) &&
                    (count($argument) == count($filtered))
                )
            ){
                return self::ThrowInvalidArgumentException($argument, __METHOD__);
            }

            return true;
        }

        /**
         * Checks if the array contains only integer  values.
         * @param array $argument the argument to validate
         * @throws \InvalidArgumentException if the valiadtion fails
         * @return boolean check result
         */
        public static function ArrayContainsIntegers($argument) {
            if
            (!
                (
                    is_array($argument) &&
                    ($filtered = array_filter($argument, 'is_int')) &&
                    (count($argument) == count($filtered))
                )
            ) {
                return self::ThrowInvalidArgumentException($argument, __METHOD__);
            }

            return true;
        }

        /**
         * Checks if the array contains the keys.
         * @param array $keys the keys which should be contained
         * @param array $argument the argument to validate
         * @throws \InvalidArgumentException if the valiadtion fails
         * @return boolean check result
         */
        public static function ArrayContainsKeys(array $keys, array $argument) {
            if
            (
                ! is_array($keys) ||
                ! is_array($argument) ||
                (
                    ($result = array_diff($keys, array_keys($argument))) &&
                    (! empty($result))
                )
            ) {
                return self::ThrowInvalidArgumentException($argument, __METHOD__);
            }

            return true;
        }

        /**
         * Checks if the argument is a boolean.
         * @param string $argument the argument to validate
         * @throws \InvalidArgumentException if the valiadtion fails
         * @return boolean check result
         */
        public static function IsBoolean($argument) {
            if (! is_bool($argument)) {
                return self::ThrowInvalidArgumentException($argument,  __METHOD__);
            }

            return true;
        }

        /**
         * Checks if the argument is an object.
         * @param object $argument the argument to validate
         * @return boolean check result
         */
        public static function IsObject($argument) {
            if (! is_object($argument)) {
                return self::ThrowInvalidArgumentException($argument, __METHOD__);
            }

            return true;
        }

        /**
         * Check if the argument is not empty.
         * @param string $argument the argument to validate
         * @throws \InvalidArgumentException if the valiadtion fails
         * @return boolean check result
         */
        public static function IsNotEmpty($argument) {
            if (empty($argument)) {
                return self::ThrowInvalidArgumentException($argument, __METHOD__);
            }

            return true;
        }

        /**
         * Checks if the argument is a string matching a regex.
         * @param array ($regex, $argument) the regex and argument to validate
         * @param integer $flag does not affect
         * @throws \InvalidArgumentException if the valiadtion fails
         * @return boolean check result
         */
        public static function MatchesRegex($regex, $argument) {
            if ((! is_string($regex)) || (! is_string($argument)) || (! preg_match($regex, $argument))) {
                return self::ThrowInvalidArgumentException(array($regex, $argument), __METHOD__);
            }

            return true;
        }

    }