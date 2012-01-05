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

    /**
     * Test suite for the Autoloader class.
     * @see Brickoo\Library\Core\Autoloader
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     * @version $Id: AutoloaderTest.php 15 2011-12-23 02:05:32Z celestino $
     */

    use Brickoo\Library\Core\Autoloader;

    // require PHPUnit Autoloader
    require_once ('PHPUnit/Autoload.php');

    /**
     * Test suite for the Repository class.
     * @see Brickoo\Library\Storage\Repository
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     * @version $Id: AutoloaderTest.php 15 2011-12-23 02:05:32Z celestino $
     */

    class AutoloaderTest extends PHPUnit_Framework_TestCase
    {

        /**
         * Holds the Autoloader  object.
         * @var Autoloader\Autoloader
         */
        public $Autoloader;

        /**
         * Set up the Autoloader object used.
         */
        public function setUp()
        {
            $this->Autoloader = new Autoloader();
        }

        /**
         * Test if the class can be created.
         * @covers Brickoo\Library\Core\Autoloader::__construct
         * @covers Brickoo\Library\Core\Autoloader::resetNamespaces
         */
        public function testAutoloaderConstructor()
        {
            $this->assertInstanceOf
            (
                '\Brickoo\Library\Core\Autoloader',
                $this->Autoloader
             );
        }

        /**
         * Test if a namespace with its path can be registered.
         * @covers Brickoo\Library\Core\Autoloader::registerNamespace
         */
        public function testRegisterNamespace()
        {
            $this->assertSame
            (
                $this->Autoloader,
                $this->Autoloader->registerNamespace('TestNamespace', dirname(__FILE__))
            );
        }

        /**
         * Test if the not valid argumentsthrows an exception.
         * @covers Brickoo\Library\Core\Autoloader::registerNamespace
         * @expectedException InvalidArgumentException
         */
        public function testRegisterNamespaceArgumentException()
        {
            $this->Autoloader->registerNamespace(array('wrongType'), null);
        }

        /**
         * Test if assigning the same namespace throws an exception.
         * @covers Brickoo\Library\Core\Autoloader::registerNamespace
         * @covers Brickoo\Library\Core\Exceptions\DuplicateNamespaceRegistrationException
         * @expectedException Brickoo\Library\Core\Exceptions\DuplicateNamespaceRegistrationException
         */
        public function testDuplicateNamespaceRegistrationException()
        {
            $this->Autoloader->registerNamespace('TestNamespace', dirname(__FILE__));
            $this->Autoloader->registerNamespace('TestNamespace', dirname(__FILE__));
        }

        /**
         * Test if a namespace with its path has been unregistered.
         * @covers Brickoo\Library\Core\Autoloader::unregisterNamespace
         */
        public function testUnregisterNamespace()
        {
            $this->Autoloader->registerNamespace('TestNamespace', dirname(__FILE__));
            $this->assertSame($this->Autoloader, $this->Autoloader->unregisterNamespace('TestNamespace'));
        }

        /**
         * Test if not assigned namespace throws an exception.
         * @covers Brickoo\Library\Core\Autoloader::unregisterNamespace
         * @covers Brickoo\Library\Core\Exceptions\NamespaceNotRegisteredException
         * @expectedException Brickoo\Library\Core\Exceptions\NamespaceNotRegisteredException
         */
        public function testNamespaceNotRegisteredException()
        {
            $this->Autoloader->unregisterNamespace('NotRegisteredNamespace');
        }

        /**
         * Test if a namespace is returned in the namespaces container.
         * @covers Brickoo\Library\Core\Autoloader::getAvailableNamespaces
         */
        public function testGetAvailableNamespaces()
        {
            $this->Autoloader->registerNamespace('TestNamespace', dirname(__FILE__));
            $this->assertContains('TESTNAMESPACE', $this->Autoloader->getAvailableNamespaces());
        }

        /**
         * Test if a namespace can be registered.
         * @covers Brickoo\Library\Core\Autoloader::isNamespaceRegistered
         */
        public function testIsNamespaceRegistered()
        {
            $this->Autoloader->registerNamespace('TestNamespace', dirname(__FILE__));
            $this->assertTrue($this->Autoloader->isNamespaceRegistered('testnamespace'));
        }

        /**
         * Test if a not registred namespace fails.
         * @covers Brickoo\Library\Core\Autoloader::isNamespaceRegistered
         */
        public function testIsNamespaceRegisteredFails()
        {
            $this->assertFalse($this->Autoloader->isNamespaceRegistered('fail'));
        }

        /** Test if an not valid argument throws an exception.
         * @covers Brickoo\Library\Core\Autoloader::isNamespaceRegistered
         * @expectedException InvalidArgumentException
         */
        public function testIsNamespaceRegisteredArgumentException()
        {
            $this->assertFalse($this->Autoloader->isNamespaceRegistered(' '));
        }

        /**
         * Test if the namespace path is returned by its namespace.
         * @covers Brickoo\Library\Core\Autoloader::getNamespacePath
         */
        public function testGetNamespacePath()
        {
            $this->Autoloader->registerNamespace('TestNamespace', dirname(__FILE__));
            $this->assertEquals(dirname(__FILE__), $this->Autoloader->getNamespacePath('testnamespace'));
        }

        /**
         * Test if an not registred namespace path fails by retrieving.
         * @covers Brickoo\Library\Core\Autoloader::getNamespacePath
         */
        public function testGetNamespacePathFails()
        {
            $this->assertFalse($this->Autoloader->getNamespacePath('doesNotExistNamespace'));
        }

        /** Test if an not valid argument throws an exception.
         * @covers Brickoo\Library\Core\Autoloader::getNamespacePath
         * @expectedException InvalidArgumentException
         */
        public function testGetNamespacePathArgumentException()
        {
            $this->Autoloader->getNamespacePath(true);
        }

        /**
         * Test if the namespace path is returned by its namespace and class name.
         * @covers Brickoo\Library\Core\Autoloader::getAbsolutePath
         */
        public function testGetAbsolutePath()
        {
            $this->Autoloader->registerNamespace('TestNamespace', dirname(__FILE__));
            $path = str_replace('/', DIRECTORY_SEPARATOR, 'path\to\the\Class');
            $this->assertEquals
            (
                dirname(__FILE__) .DIRECTORY_SEPARATOR . $path . '.php',
                $this->Autoloader->getAbsolutePath('testnamespace\path\to\the\Class')
            );
        }

        /**
         * Test if an not registered namespace path fails by retrieving the absolute path..
         * @covers Brickoo\Library\Core\Autoloader::getAbsolutePath
         */
        public function testGetAbsolutePathFails()
        {
            $this->assertFalse($this->Autoloader->getAbsolutePath('\namespace\is\not\registered'));
        }

        /** Test if an not valid argument throws an exception.
         * @covers Brickoo\Library\Core\Autoloader::getAbsolutePath
         * @expectedException InvalidArgumentException
         */
        public function testGetAbsolutePathArgumentException()
        {
            $this->Autoloader->getAbsolutePath(null);
        }

        /**
         * Test if the Registry (or any other) class can be loaded.
         * and register it after the test again for further tests.
         * @covers Brickoo\Library\Core\Autoloader::loadClass
         */
        public function testLoadClass()
        {
            $this->Autoloader->registerNamespace('Brickoo', BRICKOO_DIR);
            $this->assertTrue($this->Autoloader->loadClass('Brickoo\Library\Storage\Registry'));
        }

        /**
         * Test if the a class can not be loaded if the namespace is unknowed.
         * @covers Brickoo\Library\Core\Autoloader::loadClass
         */
        public function testLoadClassFails()
        {
            $this->assertFalse($this->Autoloader->loadClass('Namespace\not\registred'));
        }

        /** Test if a class which does exist in an kowed namespace throws an exception.
         * @covers Brickoo\Library\Core\Autoloader::loadClass
         * @covers Brickoo\Library\Core\Exceptions\AutoloadFileDoesNotExistException
         * @expectedException Brickoo\Library\Core\Exceptions\AutoloadFileDoesNotExistException
         */
        public function testAutoloadFileDoesNotExistException()
        {
            $this->Autoloader->registerNamespace('Brickoo', BRICKOO_DIR);
            $this->Autoloader->loadClass('Brickoo\Library\Storage\DoesNotExist');
        }

        /**
         * Test if the autoloader is can be registred.
         * @covers Brickoo\Library\Core\Autoloader::register
         */
        public function testRegisterAutoloader()
        {
            $this->assertSame($this->Autoloader, $this->Autoloader->register());
        }

        /**
         * Test if the registering of the same autloader throws an exception.
         * @covers Brickoo\Library\Core\Autoloader::register
         * @covers Brickoo\Library\Core\Exceptions\DuplicateAutoloaderRegistrationException
         * @expectedException Brickoo\Library\Core\Exceptions\DuplicateAutoloaderRegistrationException
         */
        public function testDuplicateAutoloaderRegistrationException()
        {
            $this->Autoloader->register();
            $this->Autoloader->register();
        }

        /**
         * Test if the autoloader can be unregistered.
         * @covers Brickoo\Library\Core\Autoloader::unregister
         */
        public function testUnregister()
        {
            $this->Autoloader->register();
            $this->assertSame($this->Autoloader, $this->Autoloader->unregister());
        }

        /**
         * Test if trying to unregister an not registered autoloader
         * throws an exception
         * @covers Brickoo\Library\Core\Autoloader::unregister
         * @covers Brickoo\Library\Core\Exceptions\AutoloaderNotRegisteredExeption
         * @expectedException Brickoo\Library\Core\Exceptions\AutoloaderNotRegisteredExeption
         */
        public function testAutoloaderNotRegisteredExeption()
        {
            $this->Autoloader->unregister();
        }

     }

?>