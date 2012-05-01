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

    use Brickoo\Core\Autoloader;

    // require PHPUnit Autoloader
    require_once ('PHPUnit/Autoload.php');

    /**
     * Test suite for the Autoloader class.
     * @see Brickoo\Core\Autoloader
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class AutoloaderTest extends PHPUnit_Framework_TestCase {

        /**
         * Holds the Autoloader  object.
         * @var \Brickoo\Core\Autoloader
         */
        public $Autoloader;

        /**
         * Set up the Autoloader object used.
         * @return void
         */
        public function setUp() {
            $this->Autoloader = new Autoloader();
        }

        /**
         * Test if the class can be created.
         * @covers Brickoo\Core\Autoloader::__construct
         */
        public function testAutoloaderConstructor() {
            $this->assertInstanceOf
            (
                '\Brickoo\Core\Autoloader',
                $this->Autoloader
             );
        }

        /**
         * Test if a namespace with its path can be registered.
         * @covers Brickoo\Core\Autoloader::registerNamespace
         */
        public function testRegisterNamespace() {
            $this->assertSame
            (
                $this->Autoloader,
                $this->Autoloader->registerNamespace('TestNamespace', dirname(__FILE__))
            );
        }

        /**
         * Test if the not valid arguments throws an exception.
         * @covers Brickoo\Core\Autoloader::registerNamespace
         * @expectedException InvalidArgumentException
         */
        public function testRegisterNamespaceArgumentException() {
            $this->Autoloader->registerNamespace(array('wrongType'), null);
        }

        /**
         * Test if the not available directory throws an exception.
         * @covers Brickoo\Core\Autoloader::registerNamespace
         * @covers Brickoo\Core\Exceptions\DirectoryDoesNotExistException
         * @expectedException Brickoo\Core\Exceptions\DirectoryDoesNotExistException
         */
        public function testRegisterNamespaceDirectoryException() {
            $this->Autoloader->registerNamespace('brickoo', 'path/does/not/exist');
        }

        /**
         * Test if assigning the same namespace throws an exception.
         * @covers Brickoo\Core\Autoloader::registerNamespace
         * @covers Brickoo\Core\Exceptions\DuplicateNamespaceRegistrationException
         * @expectedException Brickoo\Core\Exceptions\DuplicateNamespaceRegistrationException
         */
        public function testDuplicateNamespaceRegistrationException() {
            $this->Autoloader->registerNamespace('TestNamespace', dirname(__FILE__));
            $this->Autoloader->registerNamespace('TestNamespace', dirname(__FILE__));
        }

        /**
         * Test if a namespace with its path has been unregistered.
         * @covers Brickoo\Core\Autoloader::unregisterNamespace
         */
        public function testUnregisterNamespace() {
            $this->Autoloader->registerNamespace('TestNamespace', dirname(__FILE__));
            $this->assertSame($this->Autoloader, $this->Autoloader->unregisterNamespace('TestNamespace'));
        }

        /**
         * Test if not assigned namespace throws an exception.
         * @covers Brickoo\Core\Autoloader::unregisterNamespace
         * @covers Brickoo\Core\Exceptions\NamespaceNotRegisteredException
         * @expectedException Brickoo\Core\Exceptions\NamespaceNotRegisteredException
         */
        public function testNamespaceNotRegisteredException() {
            $this->Autoloader->unregisterNamespace('NotRegisteredNamespace');
        }

        /**
         * Test if a namespace is returned in the namespaces container.
         * @covers Brickoo\Core\Autoloader::getAvailableNamespaces
         */
        public function testGetAvailableNamespaces() {
            $this->Autoloader->registerNamespace('TestNamespace', dirname(__FILE__));
            $this->assertContains('TESTNAMESPACE', $this->Autoloader->getAvailableNamespaces());
        }

        /**
         * Test if a namespace can be registered.
         * @covers Brickoo\Core\Autoloader::isNamespaceRegistered
         */
        public function testIsNamespaceRegistered() {
            $this->Autoloader->registerNamespace('TestNamespace', dirname(__FILE__));
            $this->assertTrue($this->Autoloader->isNamespaceRegistered('testnamespace'));
        }

        /**
         * Test if a not registred namespace fails.
         * @covers Brickoo\Core\Autoloader::isNamespaceRegistered
         */
        public function testIsNamespaceRegisteredFails() {
            $this->assertFalse($this->Autoloader->isNamespaceRegistered('fail'));
        }

        /** Test if an not valid argument throws an exception.
         * @covers Brickoo\Core\Autoloader::isNamespaceRegistered
         * @expectedException InvalidArgumentException
         */
        public function testIsNamespaceRegisteredArgumentException() {
            $this->assertFalse($this->Autoloader->isNamespaceRegistered(' '));
        }

        /**
         * Test if the namespace path is returned by its namespace and class name.
         * @covers Brickoo\Core\Autoloader::getAbsolutePath
         */
        public function testGetAbsolutePath() {
            $this->Autoloader->registerNamespace('TestNamespace', dirname(__FILE__));
            $class = 'testnamespacepath/to/the/Class';
            $this->assertEquals
            (
                dirname(__FILE__) .DIRECTORY_SEPARATOR . $class . '.php',
                $this->Autoloader->getAbsolutePath($class)
            );
        }

        /**
         * Test if an not registered namespace path fails by retrieving the absolute path..
         * @covers Brickoo\Core\Autoloader::getAbsolutePath
         */
        public function testGetAbsolutePathFails() {
            $this->assertFalse($this->Autoloader->getAbsolutePath('\namespace\is\not\registered'));
        }

        /** Test if an not valid argument throws an exception.
         * @covers Brickoo\Core\Autoloader::getAbsolutePath
         * @expectedException InvalidArgumentException
         */
        public function testGetAbsolutePathArgumentException() {
            $this->Autoloader->getAbsolutePath(null);
        }

        /**
         * Test if the include path can be retrieved for a registered namespace.
         * @covers Brickoo\Core\Autoloader::getIncludePath
         */
        public function testGetIncludePath() {
            $this->Autoloader->registerNamespace('CompanyA', dirname(__FILE__));
            $this->assertEquals(dirname(__FILE__), $this->Autoloader->getIncludePath('CompanyA\Class'));
        }

        /**
         * Test if the include path can be retrieved for a registered namespace.
         * @covers Brickoo\Core\Autoloader::getIncludePath
         * @expectedException InvalidArgumentException
         */
        public function testGetIncludePathArgumentException() {
            $this->Autoloader->getIncludePath(array('wrongType'));
        }

        /**
         * Test if the Registry (or any other) class can be loaded.
         * and register it after the test again for further tests.
         * @covers Brickoo\Core\Autoloader::loadClass
         */
        public function testLoadClass() {
            $this->Autoloader->registerNamespace('assets', dirname(__FILE__));
            $this->assertTrue($this->Autoloader->loadClass('assets\LoadableClass'));
        }

        /**
         * Test if the a class can not be loaded if the namespace is unknowed.
         * @covers Brickoo\Core\Autoloader::loadClass
         */
        public function testLoadClassFails() {
            $this->assertFalse($this->Autoloader->loadClass('Namespace\not\registred'));
        }

        /** Test if a class which does exist in an kowed namespace throws an exception.
         * @covers Brickoo\Core\Autoloader::loadClass
         * @covers Brickoo\Core\Exceptions\AutoloadFileDoesNotExistException
         * @expectedException Brickoo\Core\Exceptions\AutoloadFileDoesNotExistException
         */
        public function testAutoloadFileDoesNotExistException() {
            $this->Autoloader->registerNamespace('Brickoo', dirname(__FILE__));
            $this->Autoloader->loadClass('Brickoo\Memory\DoesNotExist');
        }

        /**
         * Test if the autoloader is can be registred.
         * @covers Brickoo\Core\Autoloader::register
         */
        public function testRegisterAutoloader() {
            $this->assertSame($this->Autoloader, $this->Autoloader->register());
        }

        /**
         * Test if the registering of the same autloader throws an exception.
         * @covers Brickoo\Core\Autoloader::register
         * @covers Brickoo\Core\Exceptions\DuplicateAutoloaderRegistrationException
         * @expectedException Brickoo\Core\Exceptions\DuplicateAutoloaderRegistrationException
         */
        public function testDuplicateAutoloaderRegistrationException() {
            $this->Autoloader->register();
            $this->Autoloader->register();
        }

        /**
         * Test if the autoloader can be unregistered.
         * @covers Brickoo\Core\Autoloader::unregister
         */
        public function testUnregister() {
            $this->Autoloader->register();
            $this->assertSame($this->Autoloader, $this->Autoloader->unregister());
        }

        /**
         * Test if trying to unregister an not registered autoloader
         * throws an exception
         * @covers Brickoo\Core\Autoloader::unregister
         * @covers Brickoo\Core\Exceptions\AutoloaderNotRegisteredExeption
         * @expectedException Brickoo\Core\Exceptions\AutoloaderNotRegisteredExeption
         */
        public function testAutoloaderNotRegisteredExeption() {
            $this->Autoloader->unregister();
        }

     }