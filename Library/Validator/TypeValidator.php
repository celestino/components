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

    namespace Brickoo\Library\Validator;

    /**
     * ArgumentValidator
     *
     * Used for validating passed arguments to methods.
     * Throws an InvalidArgumentException if the validation fails.
     * @see http://php.net/manual/de/class.invalidargumentexception.php
     * @author Celestino Diaz Teran <celestino@users.sourceforge.net>
     * @version $Id: $
     */

    class TypeValidator
    {

        /**
         * Exception message with placeholders.
         * @var string
         */
        const ExceptionMessage = 'Validation with argument at index `%d` and flag `%s` on method `%s` failed.';

        /**
         * Flag definitions for validation cases.
         * @var integer
         */
        const FLAG_STRING_CAN_BE_EMPTY        = 1;
        const FLAG_INTEGER_CAN_NOT_BE_ZERO    = 2;
        const FLAG_ARRAY_CAN_BE_EMPTY         = 4;
        const FLAG_REGEX_NEGATIVE_CHECK       = 8;

        /**
         * Holds an instance of the ArgumentValidator.
         * @var object
         */
        protected static $Validator;

        /**
         * Throws an InvalidArgumentException.
         * @param string $method the method called for validation
         * @param integer $index the index of the paramters which throws the exception
         * @param integer $flag optional flag used
         * @throws InvalidArgumentException
         * @return void
         */
        protected function throwInvalidException($method, $index, $flag = null)
        {
            throw new \InvalidArgumentException
            (
                sprintf
                (
                    self::ExceptionMessage,
                    $index,
                    ($flag === null ? 'null' : $flag),
                    $method
                ),
                E_ERROR
            );
        }

        /**
         * Iterate through the arguments and call the passed method with each argument.
         * @param string $method the method called for validation
         * @param array $arguments the argument failed
         * @param integer $flag optional flag used
         * @return boolean true if the validation success otherwise the failed argument index
         */
        protected function checkValidation($method, array $arguments, $flag = null)
        {
            $valid = true;

            foreach($arguments as $index => $argument)
            {
                if(! call_user_func_array(array($this, $method), array($argument, $flag)))
                {
                    $valid = $index;
                    break;
                }
            }

            return $valid;
        }

        protected function isMethodAvailable($method)
        {
            return (
                is_string($method)
                &&
                method_exists(self::$Validator, $method)
            );
        }

        protected function isFlagSupported($flag)
        {
            return (
                ($flag === null) ||
                is_int($flag)
            );
        }

        /**
         * Validates the passed arguments with the desired method and flags.
         * @param string $method the method to call for validation
         * @param array $arguments the arguments to validate
         * @param integer $flag optional flag for the validation method
         * @return boolean success
         */
        public static function Validate($method, array $arguments, $flag = null)
        {
            if (self::$Validator === null)
            {
                self::$Validator = new TypeValidator();
            }

            if (! self::$Validator->isMethodAvailable($method))
            {
                throw new \BadMethodCallException('Method does not exists in the TypeValidator.', E_ERROR);
            }

            if (! self::$Validator->isFlagSupported($flag))
            {
               throw new \InvalidArgumentException('Validator flag is not supported.', E_ERROR);
            }

            if (($valid = self::$Validator->checkValidation($method, $arguments, $flag)) !== true)
            {
                return self::$Validator->throwInvalidException($method, $valid, $flag);
            }

            return true;
        }

        /**
         * Check if the argument is a string.
         * @param string $argument the argument to validate
         * @param integer $flag the flag to allow empty strings
         * @return boolean check result
         */
        public function isString($argument, $flag = null)
        {
            return (
                (
                    (
                        ($flag === null) ||
                        ($flag & self::FLAG_STRING_CAN_BE_EMPTY) === 0
                     )
                    &&
                    is_string($argument)
                    &&
                    ($argument = trim($argument))
                ) ||
                (
                    ($flag & self::FLAG_STRING_CAN_BE_EMPTY) !== 0
                    &&
                    is_string($argument)
                )
            );
        }

        /**
         * Check if the argument is a integer.
         * @param string $argument the argument to validate
         * @param integer $flag the flag to allow zero values
         * @return boolean check result
         */
        public function isInteger($argument, $flag = null)
        {
            return (
                (
                    (
                        ($flag === null) ||
                        ($flag & self::FLAG_INTEGER_CAN_NOT_BE_ZERO) === 0
                    )
                    &&
                    is_int($argument)
                ) ||
                (
                    ($flag & self::FLAG_INTEGER_CAN_NOT_BE_ZERO) !== 0
                    &&
                    is_int($argument)
                    &&
                    ($argument !== 0)
                )
            );
        }

        /**
         * Check if the argument is a array.
         * @param string $argument the argument to validate
         * @param integer $flag the flag to allow empty arrays
         * @return boolean check result
         */
        public function isArray($argument, $flag = null)
        {
            return (
                (
                    ($flag === null)
                    &&
                    is_array($argument)
                    &&
                    (! empty($argument))
                ) ||
                (
                    ($flag === self::FLAG_ARRAY_CAN_BE_EMPTY)
                    &&
                    is_array($argument)
                )
            );
        }

        /**
         * Check if the argument is boolean.
         * @param string $argument the argument to validate
         * @param integer $flag does not affect
         * @return boolean check result
         */
        public function isBoolean($argument, $flag = null)
        {
            return is_bool($argument);
        }

        /**
         * Check if the argument is not empty.
         * @param string $argument the argument to validate
         * @param integer $flag does not affect
         * @return boolean check result
         */
        public function isNotEmpty($argument, $flag = null)
        {
            return (! empty($argument));
        }

        /**
         * Check if the argument is a string or a integer.
         * @param string $argument the argument to validate
         * @param integer $flag the flag to allow empty() values
         * @return boolean check result
         */
        public function isStringOrInteger($argument, $flag  = null)
        {
            return (
                       $this->isString($argument, $flag) ||
                       $this->isInteger($argument, $flag)
            );
        }

        /**
         * Check if the argument is a string matching a regex.
         * @param array ($regex, $argument) the regex and argument to validate
         * @param integer $flag does not affect
         * @return boolean check result
         */
        public function useRegex($argument, $flag = null)
        {
            if
            (
                (! is_array($argument)) ||
                (count($argument) < 2)
            )
            {
                return false;
            }

            self::Validate('isString', array($argument[1]));

            return (
                (
                    ($flag === null)
                    &&
                    preg_match($argument[0], $argument[1])
                ) ||
                (
                    ($flag === self::FLAG_REGEX_NEGATIVE_CHECK)
                    &&
                    (! preg_match($argument[0], $argument[1]))
                )
            );
        }

    }

?>