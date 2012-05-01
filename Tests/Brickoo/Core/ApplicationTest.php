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

    use Brickoo\Core\Application;

    // require PHPUnit Autoloader
    require_once ('PHPUnit/Autoload.php');

    /**
     * Test suite for the Application class.
     * @see Brickoo\Core\Application
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class ApplicationTest extends \PHPUnit_Framework_TestCase {

        /**
         * Holds an instance of the Application class.
         * @var \Brickoo\Core\Application
         */
        protected $Application;

        /**
         * Sets up the Application instance used.
         * @return void
         */
        protected function setUp() {
            $this->Application = new Application();
        }

        /**
         * Test if the Brickoo\Memory\Registry can be lazy initialized and retrieved.
         * @covers Brickoo\Core\Application::Registry
         * @covers Brickoo\Core\Application::getDependency
         */
        public function testRegistryLazyInitialization() {
            $this->assertInstanceOf('Brickoo\Memory\Interfaces\Registry', ($Registry = $this->Application->Registry()));
            $this->assertAttributeEquals(array('Registry' => $Registry), 'dependencies', $this->Application);
        }

        /**
         * Test of the Registry instance can be injected and the Application reference is returned.
         * @covers Brickoo\Core\Application::Registry
         * @covers Brickoo\Core\Application::getDependency
         */
        public function testRegistryInjection() {
            $Registry = $this->getMock('Brickoo\Memory\Registry');
            $this->assertSame($this->Application, $this->Application->Registry($Registry));
            $this->assertAttributeEquals(array('Registry' => $Registry), 'dependencies', $this->Application);
        }

        /**
         * Test if the Brickoo\Http\Request instance can be lazy initialized.
         * @covers Brickoo\Core\Application::Request
         * @covers Brickoo\Core\Application::getDependency
         */
        public function testRequestLazyInitialization() {
            $this->assertInstanceOf('Brickoo\Http\Request', ($Request = $this->Application->Request()));
            $this->assertAttributeEquals(array('Request' => $Request), 'dependencies', $this->Application);
        }

        /**
         * Test if the Request dependency can be injected and the Application reference is returned.
         * @covers Brickoo\Core\Application::Request
         * @covers Brickoo\Core\Application::getDependency
         */
        public function testRequestInjection() {
            $Request = $this->getMock('Brickoo\Http\Request');
            $this->assertSame($this->Application, $this->Application->Request($Request));
            $this->assertAttributeEquals(array('Request' => $Request), 'dependencies', $this->Application);
        }

        /**
         * Test if the Brickoo\Routing\Router dependency can be lazy initialized.
         * @covers Brickoo\Core\Application::Router
         * @covers Brickoo\Core\Application::getDependency
         */
        public function testRouterLazyInitialization() {
            $Request = $this->getMock('Brickoo\Http\Request');
            $EventManager = $this->getMock('Brickoo\Event\Manager');
            $this->Application->Request($Request);
            $this->Application->EventManager($EventManager);

            $this->assertInstanceOf('Brickoo\Routing\Interfaces\Router', ($Router = $this->Application->Router()));
            $this->assertAttributeEquals(
                array('Router' => $Router, 'Request' => $Request, 'EventManager' => $EventManager),
                'dependencies',
                $this->Application
            );
        }

        /**
         * Test if the Router deoendency can be injected and the Application reference is returned.
         * @covers Brickoo\Core\Application::Router
         * @covers Brickoo\Core\Application::getDependency
         */
        public function testRouterInjection() {
            $Router = $this->getMock('Brickoo\Routing\Router', null, array($this->getMock('Brickoo\Http\Request')));
            $this->assertSame($this->Application, $this->Application->Router($Router));
            $this->assertAttributeEquals(array('Router' => $Router), 'dependencies', $this->Application);
        }

        /**
         * Test if the Brickoo\Routing\RequestRoute can be injected and the Application reference is returned.
         * @covers Brickoo\Core\Application::Route
         * @covers Brickoo\Core\Application::getDependency
         */
        public function testRouteInjection() {
            $RequestRoute = $this->getMock('Brickoo\Routing\RequestRoute', null, array(new \Brickoo\Routing\Route('test.route')));
            $this->assertSame($this->Application, $this->Application->Route($RequestRoute));
            $this->assertAttributeEquals(array('Route' => $RequestRoute), 'dependencies', $this->Application);
        }

        /**
         * Test if trying to retrieve the unset Route throws an exception.
         * @covers \Brickoo\Core\Exceptions\DependencyNotAvailableException
         * @expectedException \Brickoo\Core\Exceptions\DependencyNotAvailableException
         */
        public function testRouteLazyException() {
            $this->Application->Route();
        }

        /**
         * Test if the SessionManager can be lazy initialized and retrieved.
         * @covers Brickoo\Core\Application::SessionManager
         * @covers Brickoo\Core\Application::getDependency
         */
        public function testSessionManager() {
            $this->assertInstanceOf(
                'Brickoo\Http\Session\Interfaces\Manager',
                ($SessionManager = $this->Application->SessionManager())
            );

            $this->assertAttributeEquals(array('SessionManager' => $SessionManager), 'dependencies', $this->Application);
        }

        /**
         * Test if the Manager can be injected and the Application reference is returned.
         * @covers Brickoo\Core\Application::SessionManager
         * @covers Brickoo\Core\Application::getDependency
         */
        public function testSessionManagerInjection() {
            $SessionManager = $this->getMock(
                'Brickoo\Http\Session\Manager',
                null,
                array($this->getMock('Brickoo\Http\Session\Handler\CacheHandler'))
            );
            $this->assertSame($this->Application, $this->Application->SessionManager($SessionManager));
            $this->assertAttributeEquals(array('SessionManager' => $SessionManager), 'dependencies', $this->Application);
        }

        /**
         * Test if the Runner can be lazy initialized and retrieved.
         * @covers Brickoo\Core\Application::Runner
         * @covers Brickoo\Core\Application::getDependency
         */
        public function testRunner() {
            $this->assertInstanceOf(
                'Brickoo\Core\Interfaces\Runner',
                ($Runner = $this->Application->Runner())
            );

            $this->assertAttributeEquals(array('Runner' => $Runner), 'dependencies', $this->Application);
        }

        /**
         * Test if the Manager can be injected and the Application reference is returned.
         * @covers Brickoo\Core\Application::Runner
         * @covers Brickoo\Core\Application::getDependency
         */
        public function testRunnerInjection() {
            $Runner = $this->getMock('Brickoo\Core\Runner');
            $this->assertSame($this->Application, $this->Application->Runner($Runner));
            $this->assertAttributeEquals(array('Runner' => $Runner), 'dependencies', $this->Application);
        }

        /**
         * Test if the Brickoo\Event\EventManager can be lazy initialized and retrieved.
         * @covers Brickoo\Core\Application::EventManager
         * @covers Brickoo\Core\Application::getDependency
         */
        public function testEventManagerLazyIntialization() {
            $this->assertInstanceOf('Brickoo\Event\Interfaces\Manager',
                ($EventManager = $this->Application->EventManager())
            );
            $this->assertAttributeEquals(array('EventManager' => $EventManager), 'dependencies', $this->Application);
        }

        /**
         * Test if the EventManager dependency can be injected and the Application reference is returned.
         * @covers Brickoo\Core\Application::EventManager
         * @covers Brickoo\Core\Application::getDependency
         */
        public function testEventManagerInjection() {
            $EventManager =  $this->getMock('Brickoo\Event\Manager');
            $this->assertSame($this->Application, $this->Application->EventManager($EventManager));
        }

        /**
         * Test if the version can be retrieved and matches the expected regular expression.
         * @covers Brickoo\Core\Application::getVersion
         */
        public function testGetVersion() {
            $this->assertRegExp('~^([A-Z]{3,6}\-)?[0-9]\.[0-9]$~', $this->Application->getVersion());
        }

        /**
         * Test if the version number can be retrieved and matches a float value.
         * @covers Brickoo\Core\Application::getVersionNumber
         */
        public function testGetVersionNumber() {
            $this->assertRegExp('~^[0-9]\.[0-9]$~', $this->Application->getVersionNumber());
        }

        /**
         * Test if the Autoloader can be registered to the local Registry.
         * @covers Brickoo\Core\Application::registerAutoloader
         */
        public function testRegisterAutoloader() {
            $Autoloader = $this->getMock('Brickoo\Core\Autoloader');

            $Registry = $this->getMock('Brickoo\Memory\Registry', array('register', 'lock'));
            $Registry->expects($this->once())
                     ->method('register')
                     ->with('brickoo.autoloader', $Autoloader)
                     ->will($this->returnSelf());
            $Registry->expects($this->once())
                     ->method('lock')
                     ->with('brickoo.autoloader')
                     ->will($this->returnSelf());

            $this->Application->Registry($Registry);

            $this->assertSame($this->Application, $this->Application->registerAutoloader($Autoloader));
        }

        /**
         * Test if the modules can be registered.
         * @covers Brickoo\Core\Application::registerModules
         * @covers Brickoo\Core\Application::getModules
         */
        public function testModulesRoutine() {
            $modules = array(
                'Test/ModuleA' => 'path/to/module/A',
                'Test/ModuleB' => 'path/to/module/B',
            );

            $expected = array(
                'Test/ModuleA' => 'path/to/module/A'.DIRECTORY_SEPARATOR,
                'Test/ModuleB' => 'path/to/module/B'.DIRECTORY_SEPARATOR,
            );

            $Registry = $this->getMock('Brickoo\Memory\Registry', array('register', 'lock', 'isRegistered', 'get'));
            $Registry->expects($this->once())
                     ->method('register')
                     ->with('brickoo.modules', $expected)
                     ->will($this->returnSelf());
            $Registry->expects($this->once())
                     ->method('lock')
                     ->with('brickoo.modules')
                     ->will($this->returnSelf());
            $Registry->expects($this->once())
                     ->method('isRegistered')
                     ->with('brickoo.modules')
                     ->will($this->returnValue(true));
            $Registry->expects($this->once())
                     ->method('get')
                     ->with('brickoo.modules')
                     ->will($this->returnValue($expected));

            $this->Application->Registry($Registry);

            $this->assertSame($this->Application, $this->Application->registerModules($modules));
            $this->assertEquals($expected, $this->Application->getModules());
        }

        /**
         * Test if the registered modules are recognized.
         * @covers Brickoo\Core\Application::isModuleAvailable
         */
        public function testIsModuleAvailable() {
            $expected = array(
                'Test/ModuleA' => 'path/to/module/A'.DIRECTORY_SEPARATOR,
                'Test/ModuleB' => 'path/to/module/B'.DIRECTORY_SEPARATOR,
            );

            $Registry = $this->getMock('Brickoo\Memory\Registry', array('isRegistered', 'get'));
            $Registry->expects($this->exactly(2))
                     ->method('isRegistered')
                     ->with('brickoo.modules')
                     ->will($this->returnValue(true));
            $Registry->expects($this->exactly(2))
                     ->method('get')
                     ->with('brickoo.modules')
                     ->will($this->returnValue($expected));

            $this->Application->Registry($Registry);

            $this->assertTrue($this->Application->isModuleAvailable('Test/ModuleA'));
            $this->assertTrue($this->Application->isModuleAvailable('Test/ModuleB'));
        }

        /**
         * Test if the module path can be returned.
         * @covers Brickoo\Core\Application::getModulePath
         */
        public function testGetModulePath() {
            $expected = array(
                'Test/ModuleA' => 'path/to/module/A'.DIRECTORY_SEPARATOR,
                'Test/ModuleB' => 'path/to/module/B'.DIRECTORY_SEPARATOR,
            );

            $Registry = $this->getMock('Brickoo\Memory\Registry', array('isRegistered', 'get'));
            $Registry->expects($this->exactly(4))
                     ->method('isRegistered')
                     ->with('brickoo.modules')
                     ->will($this->returnValue(true));
            $Registry->expects($this->exactly(4))
                     ->method('get')
                     ->with('brickoo.modules')
                     ->will($this->returnValue($expected));

            $this->Application->Registry($Registry);

            $this->assertEquals($expected['Test/ModuleA'], $this->Application->getModulePath('Test/ModuleA'));
            $this->assertEquals($expected['Test/ModuleB'], $this->Application->getModulePath('Test/ModuleB'));
        }

        /**
         * Test if trying to retrieve a not existing path throws an exception.
         * @covers Brickoo\Core\Application::getModulePath
         * @covers Brickoo\Core\Exceptions\ModuleNotAvailableException
         * @expectedException Brickoo\Core\Exceptions\ModuleNotAvailableException
         */
        public function testModuleNotAvailableException() {
            $this->Application->getModulePath('FAILURE');
        }

        /**
         * Test if a directory path can be registerd.
         * @covers Brickoo\Core\Application::registerDirectory
         */
        public function testRegisterDirectory() {
            $Registry = $this->getMock('Brickoo\Memory\Registry', array('register', 'lock'));
            $Registry->expects($this->once())
                     ->method('register')
                     ->with('testDir', getcwd() . DIRECTORY_SEPARATOR)
                     ->will($this->returnSelf());
            $Registry->expects($this->once())
                     ->method('lock')
                     ->with('testDir')
                     ->will($this->returnSelf());

            $this->Application->Registry($Registry);

            $this->assertSame($this->Application, $this->Application->registerDirectory(
                'testDir', getcwd()
            ));
        }

        /**
         * Test if trying to register a not existing directora throws an exception.
         * @covers Brickoo\Core\Application::registerDirectory
         * @expectedException Brickoo\Core\Exceptions\DirectoryDoesNotExistException
         */
        public function testNotExistingDirectoryException() {
            $this->Application->registerDirectory('fail', '/path/does/not/exist');
        }

        /**
         * Test if the public directory can be registered.
         * @covers Brickoo\Core\Application::registerPublicDirectory
         */
        public function testRegisterPublicDirectory() {
            $publicDirectory = '/path/to/public/directory';
            $expected = $publicDirectory . '/';

            $Registry = $this->getMock('Brickoo\Memory\Registry', array('register', 'lock'));
            $Registry->expects($this->once())
                     ->method('register')
                     ->with('brickoo.public.directory', $expected)
                     ->will($this->returnSelf());
            $Registry->expects($this->once())
                     ->method('lock')
                     ->with('brickoo.public.directory')
                     ->will($this->returnSelf());

            $this->Application->Registry($Registry);

            $this->assertSame($this->Application, $this->Application->registerPublicDirectory($publicDirectory));
        }

        /**
         * Test if the  public directory is recognized as available.
         * @covers Brickoo\Core\Application::hasPublicDirectory
         */
        public function testHasPublicDirectory() {
            $Registry = $this->getMock('Brickoo\Memory\Registry', array('isRegistered'));
            $Registry->expects($this->once())
                     ->method('isRegistered')
                     ->with('brickoo.public.directory')
                     ->will($this->returnValue(true));

            $this->Application->Registry($Registry);

            $this->assertTrue($this->Application->hasPublicDirectory());
        }

        /**
         * Test if a Registry entry could be recognized.
         * @covers Brickoo\Core\Application::has
         * @covers Brickoo\Core\Application::__isset
         */
        public function testHas() {
            $Registry = $this->getMock('Brickoo\Memory\Registry', array('isRegistered'));
            $Registry->expects($this->exactly(2))
                     ->method('isRegistered')
                     ->with('brickoo.modules')
                     ->will($this->returnValue(true));

            $this->Application->Registry($Registry);

            $this->assertTrue($this->Application->has('modules'));
            $this->assertTrue(isset($this->Application->modules));
        }

        /**
         * Test if a Registry value could be retrieved.
         * @covers Brickoo\Core\Application::get
         * @covers Brickoo\Core\Application::__get
         */
        public function testGet() {
            $expected = array('SampleModule', '/sample/path/');

            $Registry = $this->getMock('Brickoo\Memory\Registry', array('isRegistered', 'get'));
            $Registry->expects($this->exactly(2))
                     ->method('isRegistered')
                     ->with('brickoo.modules')
                     ->will($this->returnValue(true));
            $Registry->expects($this->exactly(2))
                     ->method('get')
                     ->with('brickoo.modules')
                     ->will($this->returnValue($expected));

            $this->Application->Registry($Registry);

            $this->assertEquals($expected, $this->Application->get('modules'));
            $this->assertEquals($expected, $this->Application->modules);
        }

        /**
         * Test if a Registry value does not exist null is returned.
         * @covers Brickoo\Core\Application::get
         */
        public function testGetNullReturnValue() {
            $Registry = $this->getMock('Brickoo\Memory\Registry', array('isRegistered', 'get'));
            $Registry->expects($this->once())
                     ->method('isRegistered')
                     ->with('notAvailable')
                     ->will($this->returnValue(null));

            $this->Application->Registry($Registry);

            $this->assertNull($this->Application->get('notAvailable'));
        }

        /**
         * Test if Registry values could be set.
         * @covers Brickoo\Core\Application::set
         * @covers Brickoo\Core\Application::__set
         */
        public function testSet() {
            $expected = 'testValue';

            $Registry = $this->getMock('Brickoo\Memory\Registry', array('register', 'lock'));
            $Registry->expects($this->exactly(2))
                     ->method('register')
                     ->with('entry', $expected)
                     ->will($this->returnSelf());
            $Registry->expects($this->exactly(2))
                     ->method('lock')
                     ->with('entry')
                     ->will($this->returnSelf());

            $this->Application->Registry($Registry);

            $this->assertSame($this->Application, $this->Application->set('entry', $expected));
            $this->assertEquals($expected, ($this->Application->entry = $expected));
        }

        /**
         * Test if the application can be run and returns a response.
         * @covers Brickoo\Core\Events
         * @covers Brickoo\Core\Application::run
         */
        public function testRunWithResponse() {
            $RequestRoute = $this->getMock('Brickoo\Routing\RequestRoute', null, array($this->getMock(
                'Brickoo\Routing\Route', null, array('testRoute')
            )));
            $Response = $this->getMock('Brickoo\Http\Response');

            $callback = function($Event) use($Response, $RequestRoute) {
                if ($Event->getName() == \Brickoo\Core\Events::EVENT_RESPONSE_GET) {
                    return $Response;
                }
                if ($Event->getName() == \Brickoo\Core\Events::EVENT_ROUTE_GET) {
                    return $RequestRoute;
                }
            };

            $EventManager = $this->getMock('Brickoo\Event\Manager');
            $EventManager->expects($this->any())
                         ->method('ask')
                         ->will($this->returnCallback($callback));


            $MainApplication = $this->getMock('Brickoo\Http\Application', array('aggregateListeners'));
            $MainApplication->expects($this->once())
                            ->method('aggregateListeners')
                            ->with($this->isInstanceOf('Brickoo\Event\Interfaces\Manager'));

            $this->Application->EventManager($EventManager)
                              ->Runner($this->getMock('Brickoo\Core\Runner'));
            $this->assertSame($this->Application, $this->Application->run($MainApplication));
        }

        /**
         * Test if the application notifies if the response is missed.
         * @covers Brickoo\Core\Events
         * @covers Brickoo\Core\Application::run
         */
        public function testRunWithoutResponse() {
            $callbackResult = null;

            $RequestRoute = $this->getMock('Brickoo\Routing\RequestRoute', null, array($this->getMock(
                'Brickoo\Routing\Route', null, array('testRoute')
            )));

            $askCallback = function($Event) use($RequestRoute) {
                if ($Event->getName() == \Brickoo\Core\Events::EVENT_ROUTE_GET) {
                    return $RequestRoute;
                }
            };

            $notifyCallback = function($Event) use (&$callbackResult) {
                if ($Event->getName() == \Brickoo\Core\Events::EVENT_RESPONSE_MISSING) {
                    $callbackResult = 'missed';
                }
            };

            $EventManager = $this->getMock('Brickoo\Event\Manager');
            $EventManager->expects($this->any())
                         ->method('ask')
                         ->will($this->returnCallback($askCallback));
            $EventManager->expects($this->any())
                         ->method('notify')
                         ->will($this->returnCallback($notifyCallback));

            $this->Application->EventManager($EventManager)
                              ->Runner($this->getMock('Brickoo\Core\Runner'));
            $this->assertSame($this->Application, $this->Application->run());
            $this->assertEquals('missed', $callbackResult);
        }

        /**
         * Test if an exception is throwed during the the execution will be notified through an event.
         * @covers Brickoo\Core\Events
         * @covers Brickoo\Core\Application::run
         */
        public function testRunException() {
            $callbackResult = null;

            $RequestRoute = $this->getMock('Brickoo\Routing\RequestRoute', null, array($this->getMock(
                'Brickoo\Routing\Route', null, array('testRoute')
            )));

            $askCallback = function($Event) use($RequestRoute) {
                if ($Event->getName() == \Brickoo\Core\Events::EVENT_ROUTE_GET) {
                    return $RequestRoute;
                }
            };

            $notifyCallback = function($Event) use (&$callbackResult) {
                if ($Event->getName() == \Brickoo\Core\Events::EVENT_RESPONSE_MISSING) {
                    throw new \Exception('error');
                }

                if ($Event->getName() == \Brickoo\Core\Events::EVENT_ERROR) {
                    $callbackResult = $Event->getParam('Exception')->getMessage();
                }
            };

            $EventManager = $this->getMock('Brickoo\Event\Manager');
            $EventManager->expects($this->any())
                         ->method('ask')
                         ->will($this->returnCallback($askCallback));
            $EventManager->expects($this->any())
                         ->method('notify')
                         ->will($this->returnCallback($notifyCallback));

            $this->Application->EventManager($EventManager)
                              ->Runner($this->getMock('Brickoo\Core\Runner'));
            $this->assertSame($this->Application, $this->Application->run());
            $this->assertEquals('error', $callbackResult);
        }

    }