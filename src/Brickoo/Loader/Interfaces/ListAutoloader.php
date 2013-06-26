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

    namespace Brickoo\Loader\Interfaces;

    /**
     * ListAutoloader
     *
     * Defines an autoloader which can handle class autoloading by a class list.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    interface ListAutoloader extends Autoloader {

        /**
         * Register the class with the corresponding location.
         * @param string $className the class to register
         * @param string $location the absoulte location path to the class
         * @throws \InvalidArgumentException if an argument is not valid
         * @throws \Brickoo\Loader\Exceptions\FileDoesNotExist if the file does not exist
         * @throws \Brickoo\Loader\Exceptions\DuplicateClassRegistration if the class is already registered
         * @return \Brickoo\Loader\Interfaces\ListAutoloader
         */
        public function registerClass($className, $location);

        /**
         * Unregister the class available by the given name.
         * @param string $class the class to unregister from autoloader
         * @throws \InvalidArgumentException if an argument is not valid
         * @throws \Brickoo\Loader\Exceptions\ClassNotRegistered if the namespace is not registered
         * @return \Brickoo\Loader\Interfaces\ListAutoloader
         */
        public function unregisterClass($namespace);

        /**
         * Checks if the given class has been registered.
         * @param string $className the class to check
         * @throws \InvalidArgumentException if an argument is not valid
         * @return boolean check result
         */
        public function isClassRegistered($className);

        /**
         * Returns the registered classes.
         * @return array the registered classes
         */
        public function getRegisteredClasses();

    }