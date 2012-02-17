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

    use Brickoo\Core\Application;

    // require PHPUnit Autoloader
    require_once ('PHPUnit/Autoload.php');

    /**
     * Test suite for the Application class.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class ApplicationTest extends PHPUnit_Framework_TestCase
    {

        /**
         * Returns a Core\Registry stub.
         * @return \Brickoo\Core\Interfaces\RegistryInterface
         */
        public function getRegistryStub()
        {
            return $this->getMock(
                'Brickoo\Core\Interfaces\RegistryInterface',
                array('Registry', 'get', 'register', 'isRegistered')
            );
        }

        /**
         * Returns a Request stub .
         * @return \Brickoo\Core\Interfaces\DynamicInterface
         */
        public function getRequestStub()
        {
            return $this->getMock
            (
                'Brickoo\Core\Interfaces\RequestInterface',
                array('getPath', 'getMethod', 'getHost', 'getProtocol', 'getFormat')
            );
        }

        /**
         * Holds an instance of the Application object.
         * @var Brickoo\Core\Application
         */
        protected $Application;

        /**
         * Set up the Application object used.
         */
        public function setUp()
        {
            $this->Application = new Application($this->getRegistryStub(), $this->getRequestStub());
        }

        /**
         * Test if an instance of the Applciation can be created.
         * Test if the Registry is stored and can be retrieved.
         * @covers Brickoo\Core\Application::__construct
         * @covers Brickoo\Core\Application::Registry
         * @covers Brickoo\Core\Application::Request
         */
        public function testConstructor()
        {
            $Request  = $this->getRequestStub();
            $Registry = $this->getRegistryStub();
            $Registry->expects($this->exactly(2))
                     ->method('register');

            $Application = new Application($Registry, $Request);
            $this->assertInstanceOf('Brickoo\Core\Application', $Application);
            $this->assertAttributeSame($Registry, '_Registry', $Application);
            $this->assertSame($Registry, $Application->Registry());
            $this->assertAttributeSame($Request, '_Request', $Application);
            $this->assertSame($Request, $Application->Request());
        }

        /**
         * Test if the full Application version can be retrieved.
         * @covers Brickoo\Core\Application::getVersion
         */
        public function testGetVersion()
        {
            $this->assertRegExp('~^([a-z]+)?\{[0-9\.]+\}(\-[\w]+)?$~i', $this->Application->getVersion());
        }

        /**
         * Test if the numeric Application version can be retrieved.
         * @covers Brickoo\Core\Application::getVersionNumber
         */
        public function testGetVersionNumber()
        {
            $this->assertRegExp('~^[0-9\.]+$~', $this->Application->getVersionNumber());
        }

        /**
         * Test if the modules can be registered, recognized and retrieved.
         * @covers Brickoo\Core\Application::registerModules
         * @covers Brickoo\Core\Application::isModuleAvailable
         * @covers Brickoo\Core\Application::getModulePath
         */
        public function testModulesRoutine()
        {
            $modules = array('module' => '/module/path' . DIRECTORY_SEPARATOR);

            $RegistryStub = $this->Application->Registry();
            $RegistryStub->expects($this->once())
                         ->method('register')
                         ->with('application.modules', $modules)
                         ->will($this->returnSelf());
            $RegistryStub->expects($this->any())
                         ->method('isRegistered')
                         ->with('application.modules')
                         ->will($this->returnValue(true));
            $RegistryStub->expects($this->any())
                         ->method('get')
                         ->with('application.modules')
                         ->will($this->returnValue($modules));

            $this->assertSame($this->Application, $this->Application->registerModules($modules));
            $this->assertTrue($this->Application->isModuleAvailable('module'));
            $this->assertEquals('/module/path' . DIRECTORY_SEPARATOR, $this->Application->getModulePath('module'));
        }

        /**
         * Test if the availability of a module can be cheked.
         * @covers Brickoo\Core\Application::isModuleAvailable
         */
        public function testModuleIsNotAvailable()
        {
            $this->assertFalse($this->Application->isModuleAvailable('notAvailable'));
        }

        /**
         * Test if trying to retrive a module which does not exist throws an exception.
         * @covers Brickoo\Core\Application::getModulePath
         * @covers Brickoo\Core\Exceptions\ModuleNotAvailableException::__construct
         * @expectedException Brickoo\Core\Exceptions\ModuleNotAvailableException
         */
        public function testGetModulePathException()
        {
            $this->Application->getModulePath('notAvailable');
        }

        /**
         * Test if the cache directory can be registered.
         * @covers Brickoo\Core\Application::registerCacheDirectory
         */
        public function testRegisterCacheDirectory()
        {
            $path = '/path/to/cache/directory';

            $RegistryStub = $this->Application->Registry();
            $RegistryStub->expects($this->once())
                         ->method('register')
                         ->with('application.cache.directory', $path . DIRECTORY_SEPARATOR)
                         ->will($this->returnSelf());

            $this->assertSame($this->Application, $this->Application->registerCacheDirectory($path));
        }

        /**
         * Test if trying to use a wrong argument type throws an exception.
         * @covers Brickoo\Core\Application::registerCacheDirectory
         * @expectedException InvalidArgumentException
         */
        public function testRegisterCacheDirectoryArgumentException()
        {
            $this->Application->registerCacheDirectory(array('wrongType'));
        }

        /**
         * Test if the log directory can be registered and retrieved.
         * @covers Brickoo\Core\Application::registerLogDirectory
         */
        public function testRegisterLogDirectoryRoutine()
        {
            $path = '/path/to/cache/directory';

            $RegistryStub = $this->Application->Registry();
            $RegistryStub->expects($this->once())
                         ->method('register')
                         ->with('application.log.directory', $path . DIRECTORY_SEPARATOR)
                         ->will($this->returnSelf());

            $this->assertSame($this->Application, $this->Application->registerLogDirectory('/path/to/cache/directory'));
        }

        /**
         * Test if trying to use a wrong argument type throws an exception.
         * @covers Brickoo\Core\Application::registerLogDirectory
         * @expectedException InvalidArgumentException
         */
        public function testRegisterLogDirectoryArgumentException()
        {
            $this->Application->registerLogDirectory(array('wrongType'));
        }

        /**
         * Test if the value of a registered identifier can be retrieved from the Registry.
         * @covers Brickoo\Core\Application::__get
         */
        public function testMagicGet()
        {
            $Registry = $this->Application->Registry();
            $Registry->expects($this->once())
                     ->method('isRegistered')
                     ->with('application')
                     ->will($this->returnValue(true));
            $Registry->expects($this->once())
                     ->method('get')
                     ->with('application')
                     ->will($this->returnValue($this->Application));

            $this->assertSame($this->Application, $this->Application->application);
            $this->assertNull($this->Application->unregistered);
        }

        /**
         * Test if a value can be registered through the keyword register.
         * @covers Brickoo\Core\Application::__call
         */
        public function testMagicCall()
        {
            $Registry = $this->Application->Registry();
            $Registry->expects($this->once())
                     ->method('register')
                     ->with('application', $this->Application);

            $this->assertSame($this->Application, $this->Application->registerApplication($this->Application));
        }

        /**
         * Test if trying to call an unkowed method throws an exception.
         * @covers Brickoo\Core\Application::__call
         * @expectedException \BadMethodCallException
         */
        public function testMagicCallBadMethodException()
        {
            $this->Application->methodDoesNotExist();
        }

        /**
         * Test if the Router can be injected and is returned.
         * @covers Brickoo\Core\Application::Router
         */
        public function testGetRouterInjected()
        {
            $Router = $this->getMock('Brickoo\Routing\Interfaces\RouterInterface');

            $Registry = $this->Application->Registry();
            $Registry->expects($this->once())
                     ->method('isRegistered')
                     ->with('application.router')
                     ->will($this->returnValue(true));
            $Registry->expects($this->once())
                     ->method('get')
                     ->with('application.router')
                     ->will($this->returnValue($Router));

            $this->assertSame($Router, $this->Application->Router());
        }

        /**
         * Test if the Router can be lazy initialized and is returned.
         * @covers Brickoo\Core\Application::Router
         */
        public function testGetRouterLazyInitialization()
        {
            $Request = $this->getMock('Brickoo\Core\Interfaces\RequestInterface');

            $Registry = $this->Application->Registry();
            $Registry->expects($this->exactly(2))
                     ->method('isRegistered')
                     ->will($this->onConsecutiveCalls(false, true));
            $Registry->expects($this->once())
                     ->method('get')
                     ->with('application.request')
                     ->will($this->returnValue($Request));

            $this->assertInstanceOf('Brickoo\Routing\Interfaces\RouterInterface', $this->Application->Router());
        }

        /**
         * Test if the Router can be configured and the Application reference is returned.
         * @covers Brickoo\Core\Application::configureRouter
         */
        public function testConfigureRouter()
        {
            $Router = $this->getMock(
                'Brickoo\Routing\Router',
                array('setCacheDirectory', 'setModules'),
                array($this->getMock('Brickoo\Core\Interfaces\RequestInterface'))
            );
            $Router->expects($this->once())
                   ->method('setCacheDirectory')
                   ->with('/path/to/cache')
                   ->will($this->returnSelf());
            $Router->expects($this->once())
                   ->method('setModules')
                   ->with(array('ModuleA' => '/path/to/moduleA'))
                   ->will($this->returnSelf());

            $valueMap = array(
                array('application.router', $Router),
                array('application.cache.directory', '/path/to/cache'),
                array('application.modules', array('ModuleA' => '/path/to/moduleA'))
            );

            $Registry = $this->Application->Registry();
            $Registry->expects($this->any())
                     ->method('isRegistered')
                     ->will($this->returnValue(true));
            $Registry->expects($this->exactly(3))
                     ->method('get')
                     ->will($this->returnValueMap($valueMap));

            $this->assertSame($this->Application, $this->Application->configureRouter());

        }

        /**
         * Test if a conreoller implementing the ControllerInterface can be configured
         * with the dependencies.
         * @covers Brickoo\Core\Application::configureController
         */
        public function testConfigureController()
        {
            $Route = $this->getMock('Brickoo\Routing\Interfaces\RequestRouteInterface');

            $Registry = $this->Application->Registry();
            $Registry->expects($this->once())
                     ->method('isRegistered')
                     ->with('application.request.route')
                     ->will($this->returnValue(true));
            $Registry->expects($this->once())
                     ->method('get')
                     ->with('application.request.route')
                     ->will($this->returnValue($Route));

            $Controller = $this->getMock(
                'Brickoo\Core\Interfaces\ControllerInterface',
                array('Registry', 'Application', 'Route', 'Request')
            );
            $Controller->expects($this->once())
                       ->method('Registry')
                       ->with($this->Application->Registry())
                       ->will($this->returnSelf());
            $Controller->expects($this->once())
                       ->method('Application')
                       ->with($this->Application)
                       ->will($this->returnSelf());
            $Controller->expects($this->once())
                       ->method('Request')
                       ->with($this->Application->Request())
                       ->will($this->returnSelf());
            $Controller->expects($this->once())
                       ->method('Route')
                       ->with($Route)
                       ->will($this->returnSelf());

            $this->assertSame($this->Application, $this->Application->configureController($Controller));
        }


    }