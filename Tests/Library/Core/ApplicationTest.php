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
     * 3. Neither the name of Application nor the names of its contributors may be used
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

    use Brickoo\Library\Core\Application;

    // require PHPUnit Autoloader
    require_once ('PHPUnit/Autoload.php');

    /**
     * Test suite for the Application class.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class ApplicationTest extends PHPUnit_Framework_TestCase
    {

        /**
         * Creates and returns a stub of the Core\Registry.
         * @return object Registry stub
         */
        public function getRegistryMock(array $methods = null)
        {
            return $this->getMock
            (
                'Brickoo\Library\Memory\Registry',
                ($methods === null ? null : array_values($methods))
            );
        }

        /**
         * Holds an instance of the Application object.
         * @var Brickoo\Library\Core\Application
         */
        protected $ApplicationFixture;

        /**
         * Set up the Application object used.
         */
        public function setUp()
        {
            $this->ApplicationFixture = new ApplicationFixture();
        }

        /**
         * Test if the full Application version can be retrieved.
         * @covers Brickoo\Library\Core\Application::getVersion
         */
        public function testGetVersion()
        {
            $this->assertRegExp('~^([a-z]+)?\{[0-9\.]+\}(\-[\w]+)?$~i', $this->ApplicationFixture->getVersion());
        }

        /**
         * Test if the numeric Application version can be retrieved.
         * @covers Brickoo\Library\Core\Application::getVersionNumber
         */
        public function testGetVersionNumber()
        {
            $this->assertRegExp('~^[0-9\.]+$~', $this->ApplicationFixture->getVersionNumber());
        }

        /**
         * Test if injecting the Registry dependency returns the Application object reference.
         * @covers Brickoo\Library\Core\Application::injectRegistry
         */
        public function testInjectRegistry()
        {
            $RegistryStub = $this->getRegistryMock();
            $this->assertSame($this->ApplicationFixture, $this->ApplicationFixture->injectRegistry($RegistryStub));
        }

        /**
         * Test if the reassigment of an Registry throws an exception.
         * @covers Brickoo\Library\Core\Application::injectRegistry
         * @covers Brickoo\Library\Core\Exceptions\DependencyOverwriteException
         * @expectedException Brickoo\Library\Core\Exceptions\DependencyOverwriteException
         */
        public function testInjectRegistryDependencyException()
        {
            $RegistryStub = $this->getRegistryMock();
            $this->ApplicationFixture->injectRegistry($RegistryStub);
            $this->ApplicationFixture->injectRegistry($RegistryStub);
        }

        /**
         * Test if the Registry dependency can be returned.
         * @covers Brickoo\Library\Core\Application::getRegistry
         */
        public function testGetRegistry()
        {
            $RegistryStub = $this->getRegistryMock();
            $this->ApplicationFixture->injectRegistry($RegistryStub);

            $this->assertSame($RegistryStub, $this->ApplicationFixture->getRegistry());
        }

        /**
         * Test if the Registry is not avilable it will be lazy created.
         * @covers Brickoo\Library\Core\Application::getRegistry
         */
        public function testGetRegistryLazy()
        {
            $this->assertInstanceOf
            (
                'Brickoo\Library\Memory\Interfaces\RegistryInterface',
                $this->ApplicationFixture->getRegistry()
            );
        }

        /**
         * Test if a regitry identfier and its value can be registered, recognized and retrieved.
         * @covers Brickoo\Library\Core\Application::register
         * @covers Brickoo\Library\Core\Application::isRegistered
         * @covers Brickoo\Library\Core\Application::get
         */
        public function testRegistrationRoutine()
        {
            $RegistryStub = $this->getRegistryMock(array('register', 'lock', 'isRegistered', 'getRegistered'));
            $RegistryStub->expects($this->once())
                         ->method('register')
                         ->will($this->returnSelf());
            $RegistryStub->expects($this->once())
                         ->method('lock')
                         ->will($this->returnValue(true));
            $RegistryStub->expects($this->once())
                         ->method('isRegistered')
                         ->will($this->returnValue(true));
            $RegistryStub->expects($this->once())
                         ->method('getRegistered')
                         ->will($this->returnValue('the registered value'));
            $this->ApplicationFixture->injectRegistry($RegistryStub);

            $this->assertEquals($this->ApplicationFixture, $this->ApplicationFixture->register('id', 'some value'));
            $this->assertTrue($this->ApplicationFixture->isRegistered('id'));
            $this->assertEquals('the registered value', $this->ApplicationFixture->get('id'));
        }

        /**
         * Test if the Autoloader can be registered and retrieved from the registry.
         * @covers Brickoo\Library\Core\Application::registerAutoloader
         * @covers Brickoo\Library\Core\Application::getAutoloader
         */
        public function testRegisterAutoloader()
        {
            $AutoloaderStub = $this->getMock('Brickoo\Library\Core\Interfaces\AutoloaderInterface');

            $RegistryStub = $this->getRegistryMock(array('register', 'lock', 'getRegistered'));
            $RegistryStub->expects($this->once())
                         ->method('register')
                         ->will($this->returnSelf());
            $RegistryStub->expects($this->once())
                         ->method('lock')
                         ->will($this->returnValue(true));
            $RegistryStub->expects($this->once())
                         ->method('getRegistered')
                         ->will($this->returnValue($AutoloaderStub));
            $this->ApplicationFixture->injectRegistry($RegistryStub);

            $this->assertSame($this->ApplicationFixture, $this->ApplicationFixture->registerAutoloader($AutoloaderStub));
            $this->assertSame($AutoloaderStub, $this->ApplicationFixture->getAutoloader());
        }

        /**
         * Test if the modules can be registered, recognized and retrieved.
         * @covers Brickoo\Library\Core\Application::registerModules
         * @covers Brickoo\Library\Core\Application::getModules
         * @covers Brickoo\Library\Core\Application::isModuleAvailable
         * @covers Brickoo\Library\Core\Application::getModulePath
         */
        public function testModulesRoutine()
        {
            $AutoloaderStub = $this->getMock('Brickoo\Library\Core\Autoloader', array('registerNamespace'));
            $AutoloaderStub->expects($this->once())
                           ->method('registerNamespace')
                           ->will($this->returnValue(true));

            $modules = array('module' => '/module/path');

            $valueMap = array
            (
                array(Application::AUTOLOADER, $AutoloaderStub),
                array(Application::MODULES, $modules)
            );

            $RegistryStub = $this->getRegistryMock(array('register', 'lock', 'isRegistered', 'getRegistered'));
            $RegistryStub->expects($this->once())
                         ->method('register')
                         ->will($this->returnSelf());
            $RegistryStub->expects($this->once())
                         ->method('lock')
                         ->will($this->returnValue(true));
            $RegistryStub->expects($this->any())
                         ->method('isRegistered')
                         ->will($this->returnValue(true));
            $RegistryStub->expects($this->any())
                         ->method('getRegistered')
                         ->will($this->returnValueMap($valueMap));
            $this->ApplicationFixture->injectRegistry($RegistryStub);


            $this->assertSame($this->ApplicationFixture, $this->ApplicationFixture->registerModules($modules));
            $this->assertEquals($modules, $this->ApplicationFixture->getModules());
            $this->assertTrue($this->ApplicationFixture->isModuleAvailable('module'));
            $this->assertEquals('/module/path', $this->ApplicationFixture->getModulePath('module'));
        }

        /**
         * Test if the availability of a module can be cheked.
         * @covers Brickoo\Library\Core\Application::isModuleAvailable
         */
        public function testModuleIsNotAvailable()
        {
            $this->assertFalse($this->ApplicationFixture->isModuleAvailable('notAvailable'));
        }

        /**
         * Test if trying to retrive a module which does not exist throws an exception.
         * @covers Brickoo\Library\Core\Application::getModulePath
         * @covers Brickoo\Library\Core\Exceptions\ModuleNotAvailableException::__construct
         * @expectedException Brickoo\Library\Core\Exceptions\ModuleNotAvailableException
         */
        public function testGetModulePathException()
        {
            $this->ApplicationFixture->getModulePath('notAvailable');
        }

        /**
         * Test if the environment can be registered and recognized.
         * @covers Brickoo\Library\Core\Application::registerEnvironment
         * @covers Brickoo\Library\Core\Application::isEnvironment
         */
        public function testEnvironmentRoutine()
        {
            $RegistryStub = $this->getRegistryMock(array('register', 'lock', 'getRegistered'));
            $RegistryStub->expects($this->once())
                         ->method('register')
                         ->will($this->returnSelf());
            $RegistryStub->expects($this->once())
                         ->method('lock')
                         ->will($this->returnValue(true));
            $RegistryStub->expects($this->exactly(2))
                         ->method('getRegistered')
                         ->will($this->returnValue(Application::ENVIRONMENT_DEVELOPMENT));
            $this->ApplicationFixture->injectRegistry($RegistryStub);

            $this->assertSame($this->ApplicationFixture, $this->ApplicationFixture->registerEnvironment(Application::ENVIRONMENT_DEVELOPMENT));
            $this->assertTrue($this->ApplicationFixture->isEnvironment(Application::ENVIRONMENT_DEVELOPMENT));
            $this->assertFalse($this->ApplicationFixture->isEnvironment(Application::ENVIRONMENT_PRODUCTION));
        }

        /**
         * Test if trying to use a wrong argument type throws an exception.
         * @covers Brickoo\Library\Core\Application::registerEnvironment
         * @expectedException InvalidArgumentException
         */
        public function testRegisterEnvironmentArgumentException()
        {
            $this->ApplicationFixture->registerEnvironment('wrongType');
        }

        /**
         * Test if trying to use a wrong argument type throws an exception.
         * @covers Brickoo\Library\Core\Application::isEnvironment
         * @expectedException InvalidArgumentException
         */
        public function testIsEnvironmentArgumentException()
        {
            $this->ApplicationFixture->isEnvironment('wrongType');
        }

        /**
         * Test if the cache directory can be registered and retrieved.
         * @covers Brickoo\Library\Core\Application::registerCacheDirectory
         * @covers Brickoo\Library\Core\Application::getCacheDirectory
         */
        public function testCacheDirectoryRoutine()
        {
            $RegistryStub = $this->getRegistryMock(array('register', 'lock', 'getRegistered'));
            $RegistryStub->expects($this->once())
                         ->method('register')
                         ->will($this->returnSelf());
            $RegistryStub->expects($this->once())
                         ->method('lock')
                         ->will($this->returnValue(true));
            $RegistryStub->expects($this->once())
                         ->method('getRegistered')
                         ->will($this->returnValue('/path/to/cache/directory'));
            $this->ApplicationFixture->injectRegistry($RegistryStub);

            $this->assertSame($this->ApplicationFixture, $this->ApplicationFixture->registerCacheDirectory('/path/to/cache/directory'));
            $this->assertEquals('/path/to/cache/directory', $this->ApplicationFixture->getCacheDirectory());
        }

        /**
         * Test if trying to use a wrong argument type throws an exception.
         * @covers Brickoo\Library\Core\Application::registerCacheDirectory
         * @expectedException InvalidArgumentException
         */
        public function testRegisterCacheDirectoryArgumentException()
        {
            $this->ApplicationFixture->registerCacheDirectory(array('wrongType'));
        }

        /**
         * Test if the log directory can be registered and retrieved.
         * @covers Brickoo\Library\Core\Application::registerLogDirectory
         * @covers Brickoo\Library\Core\Application::getLogDirectory
         */
        public function testLogDirectoryRoutine()
        {
            $RegistryStub = $this->getRegistryMock(array('register', 'lock', 'getRegistered'));
            $RegistryStub->expects($this->once())
                         ->method('register')
                         ->will($this->returnSelf());
            $RegistryStub->expects($this->once())
                         ->method('lock')
                         ->will($this->returnValue(true));
            $RegistryStub->expects($this->once())
                         ->method('getRegistered')
                         ->will($this->returnValue('/path/to/cache/directory'));
            $this->ApplicationFixture->injectRegistry($RegistryStub);

            $this->assertSame($this->ApplicationFixture, $this->ApplicationFixture->registerLogDirectory('/path/to/cache/directory'));
            $this->assertEquals('/path/to/cache/directory', $this->ApplicationFixture->getLogDirectory());
        }

        /**
         * Test if trying to use a wrong argument type throws an exception.
         * @covers Brickoo\Library\Core\Application::registerLogDirectory
         * @expectedException InvalidArgumentException
         */
        public function testRegisterLogDirectoryArgumentException()
        {
            $this->ApplicationFixture->registerLogDirectory(array('wrongType'));
        }

        /**
        * Test if the CacheManager can be registered and the Application reference is returned.
        * Test if the CacheManager instance can be retrieved again.
        * @covers Brickoo\Library\Core\Application::registerCacheManager
        * @covers Brickoo\Library\Core\Application::getCacheManager
        */
        public function testRegisterCacheManager()
        {
            $Mock = $this->getMock('Brickoo\Library\Cache\Interfaces\CacheManagerInterface');

            $RegistryStub = $this->getRegistryMock(array('register', 'lock', 'getRegistered'));
            $RegistryStub->expects($this->once())
                         ->method('register')
                         ->will($this->returnSelf());
            $RegistryStub->expects($this->once())
                         ->method('lock')
                         ->will($this->returnValue(true));
            $RegistryStub->expects($this->once())
                         ->method('getRegistered')
                         ->will($this->returnValue($Mock));
            $this->ApplicationFixture->injectRegistry($RegistryStub);

            $this->assertSame($this->ApplicationFixture, $this->ApplicationFixture->registerCacheManager($Mock));
            $this->assertSame($Mock, $this->ApplicationFixture->getCacheManager());
        }

        /**
         * Test if a the value of a registered identifier can be retrieved.
         * @covers Brickoo\Library\Core\Application::__get
         */
        public function testMagicGet()
        {
            $RegistryStub = $this->getRegistryMock(array('register', 'lock', 'getRegistered'));
            $RegistryStub->expects($this->once())
                         ->method('getRegistered')
                         ->will($this->returnValue('some value'));
            $this->ApplicationFixture->injectRegistry($RegistryStub);

            $this->assertEquals('some value', $this->ApplicationFixture->id);
        }

        /**
         * Test if the identifier and its value can be registered.
         * @covers Brickoo\Library\Core\Application::__set
         */
        public function testMagicSet()
        {
            $RegistryStub = $this->getRegistryMock(array('register', 'lock'));
            $RegistryStub->expects($this->once())
                         ->method('register')
                         ->will($this->returnSelf());
            $RegistryStub->expects($this->once())
                         ->method('lock')
                         ->will($this->returnValue(true));
            $this->ApplicationFixture->injectRegistry($RegistryStub);

            $this->assertEquals('some value', ($this->ApplicationFixture->id = 'some value'));
        }

        /**
         * Test if a egistered identifier can be recognized
         * @covers Brickoo\Library\Core\Application::__isset
         */
        public function testMagicIsset()
        {
            $RegistryStub = $this->getRegistryMock(array('isRegistered'));
            $RegistryStub->expects($this->once())
                         ->method('isRegistered')
                         ->will($this->returnValue(true));
            $this->ApplicationFixture->injectRegistry($RegistryStub);

            $this->assertTrue(isset($this->ApplicationFixture->id));
        }


    }

    /**
     * Fixture needed to reset the static Registry assigned.
     */
    class ApplicationFixture extends Application
    {
        public function __construct()
        {
            static::$Registry = null;
        }
    }