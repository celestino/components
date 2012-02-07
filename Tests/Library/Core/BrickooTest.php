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

    use Brickoo\Library\Core\Brickoo;
    use Brickoo\Library\Memory\Registry;

    // require PHPUnit Autoloader
    require_once ('PHPUnit/Autoload.php');

    /**
     * Test suite for the Brickoo class.
     * @see Brickoo\Library\Core\BrickooObject
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class BrickooTest extends PHPUnit_Framework_TestCase
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
         * Holds an instance of the Brickoo object.
         * @var Brickoo\Library\Core\Brickoo
         */
        protected $BrickooFixture;

        /**
         * Set up the Brickoo object used.
         */
        public function setUp()
        {
            $this->BrickooFixture = new BrickooFixture();
        }

        /**
         * Test if the full BrickOO version can be retrieved.
         * @covers Brickoo\Library\Core\Brickoo::getVersion
         */
        public function testGetVersion()
        {
            $this->assertRegExp('~^([a-z]+)?\{[0-9\.]+\}(\-[\w]+)?$~i', $this->BrickooFixture->getVersion());
        }

        /**
         * Test if the numeric BrickOO version can be retrieved.
         * @covers Brickoo\Library\Core\Brickoo::getVersionNumber
         */
        public function testGetVersionNumber()
        {
            $this->assertRegExp('~^[0-9\.]+$~', $this->BrickooFixture->getVersionNumber());
        }

        /**
         * Test if injecting the Registry dependency returns the Brickoo object reference.
         * @covers Brickoo\Library\Core\Brickoo::injectRegistry
         */
        public function testInjectRegistry()
        {
            $RegistryStub = $this->getRegistryMock();
            $this->assertSame($this->BrickooFixture, $this->BrickooFixture->injectRegistry($RegistryStub));
        }

        /**
         * Test if the reassigment of an Registry throws an exception.
         * @covers Brickoo\Library\Core\Brickoo::injectRegistry
         * @covers Brickoo\Library\Core\Exceptions\DependencyOverwriteException
         * @expectedException Brickoo\Library\Core\Exceptions\DependencyOverwriteException
         */
        public function testInjectRegistryDependencyException()
        {
            $RegistryStub = $this->getRegistryMock();
            $this->BrickooFixture->injectRegistry($RegistryStub);
            $this->BrickooFixture->injectRegistry($RegistryStub);
        }

        /**
         * Test if the Registry dependency can be returned.
         * @covers Brickoo\Library\Core\Brickoo::getRegistry
         */
        public function testGetRegistry()
        {
            $RegistryStub = $this->getRegistryMock();
            $this->BrickooFixture->injectRegistry($RegistryStub);

            $this->assertSame($RegistryStub, $this->BrickooFixture->getRegistry());
        }

        /**
         * Test if the Registry is not avilable it will be lazy created.
         * @covers Brickoo\Library\Core\Brickoo::getRegistry
         */
        public function testGetRegistryLazy()
        {
            $this->assertInstanceOf
            (
                'Brickoo\Library\Memory\Interfaces\RegistryInterface',
                $this->BrickooFixture->getRegistry()
            );
        }

        /**
         * Test if a regitry identfier and its value can be registered, recognized and retrieved.
         * @covers Brickoo\Library\Core\Brickoo::register
         * @covers Brickoo\Library\Core\Brickoo::isRegistered
         * @covers Brickoo\Library\Core\Brickoo::get
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
            $this->BrickooFixture->injectRegistry($RegistryStub);

            $this->assertEquals($this->BrickooFixture, $this->BrickooFixture->register('id', 'some value'));
            $this->assertTrue($this->BrickooFixture->isRegistered('id'));
            $this->assertEquals('the registered value', $this->BrickooFixture->get('id'));
        }

        /**
         * Test if the Autoloader can be registered and retrieved from the registry.
         * @covers Brickoo\Library\Core\Brickoo::registerAutoloader
         * @covers Brickoo\Library\Core\Brickoo::getAutoloader
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
            $this->BrickooFixture->injectRegistry($RegistryStub);

            $this->assertSame($this->BrickooFixture, $this->BrickooFixture->registerAutoloader($AutoloaderStub));
            $this->assertSame($AutoloaderStub, $this->BrickooFixture->getAutoloader());
        }

        /**
         * Test if the modules can be registered, recognized and retrieved.
         * @covers Brickoo\Library\Core\Brickoo::registerModules
         * @covers Brickoo\Library\Core\Brickoo::getModules
         * @covers Brickoo\Library\Core\Brickoo::isModuleAvailable
         * @covers Brickoo\Library\Core\Brickoo::getModulePath
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
                array(Brickoo::AUTOLOADER, $AutoloaderStub),
                array(Brickoo::MODULES, $modules)
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
            $this->BrickooFixture->injectRegistry($RegistryStub);


            $this->assertSame($this->BrickooFixture, $this->BrickooFixture->registerModules($modules));
            $this->assertEquals($modules, $this->BrickooFixture->getModules());
            $this->assertTrue($this->BrickooFixture->isModuleAvailable('module'));
            $this->assertEquals('/module/path', $this->BrickooFixture->getModulePath('module'));
        }

        /**
         * Test if the availability of a module can be cheked.
         * @covers Brickoo\Library\Core\Brickoo::isModuleAvailable
         */
        public function testModuleIsNotAvailable()
        {
            $this->assertFalse($this->BrickooFixture->isModuleAvailable('notAvailable'));
        }

        /**
         * Test if trying to retrive a module which does not exist throws an exception.
         * @covers Brickoo\Library\Core\Brickoo::getModulePath
         * @covers Brickoo\Library\Core\Exceptions\ModuleNotAvailableException::__construct
         * @expectedException Brickoo\Library\Core\Exceptions\ModuleNotAvailableException
         */
        public function testGetModulePathException()
        {
            $this->BrickooFixture->getModulePath('notAvailable');
        }

        /**
         * Test if the environment can be registered and recognized.
         * @covers Brickoo\Library\Core\Brickoo::registerEnvironment
         * @covers Brickoo\Library\Core\Brickoo::isEnvironment
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
                         ->will($this->returnValue(Brickoo::ENVIRONMENT_DEVELOPMENT));
            $this->BrickooFixture->injectRegistry($RegistryStub);

            $this->assertSame($this->BrickooFixture, $this->BrickooFixture->registerEnvironment(Brickoo::ENVIRONMENT_DEVELOPMENT));
            $this->assertTrue($this->BrickooFixture->isEnvironment(Brickoo::ENVIRONMENT_DEVELOPMENT));
            $this->assertFalse($this->BrickooFixture->isEnvironment(Brickoo::ENVIRONMENT_PRODUCTION));
        }

        /**
         * Test if trying to use a wrong argument type throws an exception.
         * @covers Brickoo\Library\Core\Brickoo::registerEnvironment
         * @expectedException InvalidArgumentException
         */
        public function testRegisterEnvironmentArgumentException()
        {
            $this->BrickooFixture->registerEnvironment('wrongType');
        }

        /**
         * Test if trying to use a wrong argument type throws an exception.
         * @covers Brickoo\Library\Core\Brickoo::isEnvironment
         * @expectedException InvalidArgumentException
         */
        public function testIsEnvironmentArgumentException()
        {
            $this->BrickooFixture->isEnvironment('wrongType');
        }

        /**
         * Test if the cache directory can be registered and retrieved.
         * @covers Brickoo\Library\Core\Brickoo::registerCacheDirectory
         * @covers Brickoo\Library\Core\Brickoo::getCacheDirectory
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
            $this->BrickooFixture->injectRegistry($RegistryStub);

            $this->assertSame($this->BrickooFixture, $this->BrickooFixture->registerCacheDirectory('/path/to/cache/directory'));
            $this->assertEquals('/path/to/cache/directory', $this->BrickooFixture->getCacheDirectory());
        }

        /**
         * Test if trying to use a wrong argument type throws an exception.
         * @covers Brickoo\Library\Core\Brickoo::registerCacheDirectory
         * @expectedException InvalidArgumentException
         */
        public function testRegisterCacheDirectoryArgumentException()
        {
            $this->BrickooFixture->registerCacheDirectory(array('wrongType'));
        }

        /**
         * Test if the log directory can be registered and retrieved.
         * @covers Brickoo\Library\Core\Brickoo::registerLogDirectory
         * @covers Brickoo\Library\Core\Brickoo::getLogDirectory
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
            $this->BrickooFixture->injectRegistry($RegistryStub);

            $this->assertSame($this->BrickooFixture, $this->BrickooFixture->registerLogDirectory('/path/to/cache/directory'));
            $this->assertEquals('/path/to/cache/directory', $this->BrickooFixture->getLogDirectory());
        }

        /**
         * Test if trying to use a wrong argument type throws an exception.
         * @covers Brickoo\Library\Core\Brickoo::registerLogDirectory
         * @expectedException InvalidArgumentException
         */
        public function testRegisterLogDirectoryArgumentException()
        {
            $this->BrickooFixture->registerLogDirectory(array('wrongType'));
        }

        /**
        * Test if the FrontController can be registered and the Brickoo reference is returned.
        * Test if the FrontController instanec can be retrieved again.
        * @covers Brickoo\Library\Core\Brickoo::registerFrontController
        * @covers Brickoo\Library\Core\Brickoo::getFrontController
        */
        public function testRegisterFrontController()
        {
            $Mock = $this->getMock('Brickoo\Library\Http\Interfaces\FrontControllerInterface');

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
            $this->BrickooFixture->injectRegistry($RegistryStub);

            $this->assertSame($this->BrickooFixture, $this->BrickooFixture->registerFrontController($Mock));
            $this->assertSame($Mock, $this->BrickooFixture->getFrontController());
        }

        /**
        * Test if the CacheManager can be registered and the Brickoo reference is returned.
        * Test if the CacheManager instance can be retrieved again.
        * @covers Brickoo\Library\Core\Brickoo::registerCacheManager
        * @covers Brickoo\Library\Core\Brickoo::getCacheManager
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
            $this->BrickooFixture->injectRegistry($RegistryStub);

            $this->assertSame($this->BrickooFixture, $this->BrickooFixture->registerCacheManager($Mock));
            $this->assertSame($Mock, $this->BrickooFixture->getCacheManager());
        }

        /**
         * Test if a the value of a registered identifier can be retrieved.
         * @covers Brickoo\Library\Core\Brickoo::__get
         */
        public function testMagicGet()
        {
            $RegistryStub = $this->getRegistryMock(array('register', 'lock', 'getRegistered'));
            $RegistryStub->expects($this->once())
                         ->method('getRegistered')
                         ->will($this->returnValue('some value'));
            $this->BrickooFixture->injectRegistry($RegistryStub);

            $this->assertEquals('some value', $this->BrickooFixture->id);
        }

        /**
         * Test if the identifier and its value can be registered.
         * @covers Brickoo\Library\Core\Brickoo::__set
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
            $this->BrickooFixture->injectRegistry($RegistryStub);

            $this->assertEquals('some value', ($this->BrickooFixture->id = 'some value'));
        }

        /**
         * Test if a egistered identifier can be recognized
         * @covers Brickoo\Library\Core\Brickoo::__isset
         */
        public function testMagicIsset()
        {
            $RegistryStub = $this->getRegistryMock(array('isRegistered'));
            $RegistryStub->expects($this->once())
                         ->method('isRegistered')
                         ->will($this->returnValue(true));
            $this->BrickooFixture->injectRegistry($RegistryStub);

            $this->assertTrue(isset($this->BrickooFixture->id));
        }


    }

    /**
     * Fixture needed to reset the static Registry assigned.
     */
    class BrickooFixture extends Brickoo
    {
        public function __construct()
        {
            static::$Registry = null;
        }
    }