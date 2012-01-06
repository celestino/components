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

    namespace Brickoo\Library\Routing;

    use Brickoo\Library\Routing\Interfaces;
    use Brickoo\Library\Validator\TypeValidator;

    /**
     * Route
     *
     * Implents a Route which can be configured to handle requests
     * which execute the assigned controller and action.<ss
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     * @version $Id $
     */

    class Route implements Interfaces\RouteInterface, \Countable
    {

        /**
         * Holds the route path listening to.
         * @var string
         */
        protected $path;

        /**
         * Returns the route path listening to.
         * @throws UnexpectedValueException if the path is null
         * @return string the route path listening to
         */
        public function getPath()
        {
            if ($this->path === null)
            {
                throw new \UnexpectedValueException('The route path is `null`.');
            }

            return $this->path;
        }

        /**
         * Sets the route path to listen to.
         * @param string $path the path to liste to
         * @return object reference
         */
        public function setPath($path)
        {
            TypeValidator::IsString($path);

            $this->path = $path;

            return $this;
        }

        /**
         * Holds the controller:method which should be executed.
         * @var string
         */
        protected $controller;

        /**
         * Returns the controller:method to execute.
         * @throws UnexpectedValueException if the controller is null
         * @return string the controller and method to execute
         */
        public function getController()
        {
            if ($this->controller === null)
            {
                throw new \UnexpectedValueException('The route controller is `null`.');
            }

            return $this->controller;
        }

        /**
         * Sets the controller:method to execute.
         * The controller and method has to be seperated by ':'.
         * @param string $controller the controller and method to execute
         * @return object reference
         */
        public function setController($controller)
        {
            TypeValidator::MatchesRegex('~^[\w]+\:\:[\w]+$~', $controller);

            $this->controller = $controller;

            return $this;
        }

        /**
         * Holds the request method to listen to.
         * @var string
         */
        protected $method;

        /**
         * Returns the listening request method.
         * @throws UnexpectedValueException if the method is null
         * @return string the request method listening
         */
        public function getMethod()
        {
            if ($this->method === null)
            {
                throw new \UnexpectedValueException('The route method is `null`.');
            }

            return $this->method;
        }

        /**
         * Sets the request method to listen to.
         * @param string $method the request method to listen
         * @return object reference
         */
        public function setMethod($method)
        {
            TypeValidator::IsString($method);

            $this->method = $method;

            return $this;
        }

        /**
         * Holds the default key-values for the method parameters.
         * @var array
         */
        protected $defaultValues;

        /**
         * Returns all the default values available.
         * @return array the default key-values
         */
        public function getDefaultValues()
        {
            return $this->defaultValues;
        }

        /**
         * Returns the default value of the passed parameter name.
         * @param string $parameterName the parameter name
         * @throws UnexpectedValueException if the parameter has not an default value
         * @return mixed the default value for the passed parameter name
         */
        public function getDefaultValue($parameterName)
        {
            TypeValidator::IsString($parameterName);

            if (! $this->hasDefaultValue($parameterName))
            {
                throw new \UnexpectedValueException
                (
                    sprintf('The default value for `%s`does not exist.', $parameterName)
                );
            }

            return $this->defaultValues[$parameterName];
        }

        /**
         * Checks if the parameter has an default value.
         * @param string $parameterName the parameter to check
         * @return boolean check result
         */
        public function hasDefaultValue($parameterName)
        {
            TypeValidator::IsString($parameterName);

            return array_key_exists($parameterName, $this->defaultValues);
        }

        /**
         * Adds an parameter and his default value.
         * If the parameter name does exists it will be overwritten !
         * @param string $parameterName the parameter name to add the value to
         * @param mixed $defaultValue the default value to add
         * @return object reference
         */
        public function addDefaultValue($parameterName, $defaultValue)
        {
            TypeValidator::IsString($parameterName);

            $this->defaultValues[$parameterName] = $defaultValue;

            return $this;
        }

        /**
         * Holds the rules for the path parameters.
         * @var array
         */
        protected $rules;

        /**
         * Returns all the rules available.
         * @return array the rules
         */
        public function getRules()
        {
            return $this->rules;
        }

        /**
         * Returns the rule for the passed parameter name.
         * @param string $parameterName the parameter name to retrieve the rule for
         * @throws UnexpectedValueException if the parameter name has not an defualt value
         * @return string the rule assigned to the parameter name
         */
        public function getRule($parameterName)
        {
            TypeValidator::IsString($parameterName);

            if (! $this->hasRule($parameterName))
            {
                throw new \UnexpectedValueException
                (
                    sprintf('The rule for `%s` does not exits.', $parameterName)
                );
            }

            return $this->rules[$parameterName];
        }

        /**
         * Adds a rule to a parameter name.
         * @param string $parameterName the parameter name to add the rule to
         * @param string $rule the rule to add
         * @return object reference
         */
        public function addRule($parameterName, $rule)
        {
            TypeValidator::IsString($parameterName);
            TypeValidator::isString($rule);

            $this->rules[$parameterName] = $rule;

            return $this;
        }

        /**
         * Checks if the parameter has an rule to match.
         * @param string $parameterName the parameter name to check
         * @return boolean check result
         */
        public function hasRule($parameterName)
        {
            TypeValidator::IsString($parameterName);

            return array_key_exists($parameterName, $this->rules);
        }

        /**
        * Class constructor.
        * Initializes the class properties.
        * @return void
        */
        public function __construct()
        {
            $this->reset();
        }

        /**
        * Clears the class properties.
        * @return object reference
        */
        public function reset()
        {
            $this->controller        = null;
            $this->path              = null;
            $this->method            = null;
            $this->defaultValues     = array();
            $this->rules             = array();

            return $this;
        }

        /**
         * Returns the number of segments the path contains.
         * @see Countable::count()
         * @return integer number of segments
         */
        public function count()
        {
            return substr_count($this->getPath(), '/');
        }

    }

?>