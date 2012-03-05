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

    class ApplicationTest extends \PHPUnit_Framework_TestCase
    {

        public function getRegistryStub(array $mockedMethods = null)
        {
            return $this->getMock('Brickoo\Memory\Registry', $mockedMethods);
        }

        public function getRequestStub(array $mockedMethods = null)
        {
            return $this->getMock('Brickoo\Core\Interfaces\RequestInterface', $mockedMethods);
        }

        public function getRouterStub(array $mockedMethods = null)
        {
            return $this->getMock('Brickoo\Routing\Router', $mockedMethods);
        }

        public function getRequestRouteStub(array $mockedMethods = null)
        {
            return $this->getMock('Brickoo\Routing\RequestRoute', $mockedMethods);
        }

        public function getSessionManagerStub(array $mockedMethods = null)
        {
            return $this->getMock('Brickoo\Http\Session\SessionManager', $mockedMethods);
        }

        public function getEventManagerStub(array $mockedMethods = null)
        {
            return $this->getMock('Brickoo\Event\EventManager', $mockedMethods);
        }

        /**
         * Holds an instance of the Application class.
         * @var \Brickoo\Core\Application
         */
        protected $Application;

        /**
         * Sets up the fixture, for example, opens a network connection.
         * This method is called before a test is executed.
         */
        protected function setUp()
        {
            $this->Application = new Application();
        }

        /**
         * @covers Brickoo\Core\Application::Registry
         * @todo Implement testRegistry().
         */
        public function testRegistry()
        {
            // Remove the following lines when you implement this test.
            $this->markTestIncomplete(
              'This test has not been implemented yet.'
            );
        }

        /**
         * @covers Brickoo\Core\Application::Request
         * @todo Implement testRequest().
         */
        public function testRequest()
        {
            // Remove the following lines when you implement this test.
            $this->markTestIncomplete(
              'This test has not been implemented yet.'
            );
        }

        /**
         * @covers Brickoo\Core\Application::Router
         * @todo Implement testRouter().
         */
        public function testRouter()
        {
            // Remove the following lines when you implement this test.
            $this->markTestIncomplete(
              'This test has not been implemented yet.'
            );
        }

        /**
         * @covers Brickoo\Core\Application::Route
         * @todo Implement testRoute().
         */
        public function testRoute()
        {
            // Remove the following lines when you implement this test.
            $this->markTestIncomplete(
              'This test has not been implemented yet.'
            );
        }

        /**
         * @covers Brickoo\Core\Application::SessionManager
         * @todo Implement testSessionManager().
         */
        public function testSessionManager()
        {
            // Remove the following lines when you implement this test.
            $this->markTestIncomplete(
              'This test has not been implemented yet.'
            );
        }

        /**
         * @covers Brickoo\Core\Application::EventManager
         * @todo Implement testEventManager().
         */
        public function testEventManager()
        {
            // Remove the following lines when you implement this test.
            $this->markTestIncomplete(
              'This test has not been implemented yet.'
            );
        }

        /**
         * @covers Brickoo\Core\Application::getVersion
         * @todo Implement testGetVersion().
         */
        public function testGetVersion()
        {
            // Remove the following lines when you implement this test.
            $this->markTestIncomplete(
              'This test has not been implemented yet.'
            );
        }

        /**
         * @covers Brickoo\Core\Application::getVersionNumber
         * @todo Implement testGetVersionNumber().
         */
        public function testGetVersionNumber()
        {
            // Remove the following lines when you implement this test.
            $this->markTestIncomplete(
              'This test has not been implemented yet.'
            );
        }

        /**
         * @covers Brickoo\Core\Application::registerAutoloader
         * @todo Implement testRegisterAutoloader().
         */
        public function testRegisterAutoloader()
        {
            // Remove the following lines when you implement this test.
            $this->markTestIncomplete(
              'This test has not been implemented yet.'
            );
        }

        /**
         * @covers Brickoo\Core\Application::registerModules
         * @todo Implement testRegisterModules().
         */
        public function testRegisterModules()
        {
            // Remove the following lines when you implement this test.
            $this->markTestIncomplete(
              'This test has not been implemented yet.'
            );
        }

        /**
         * @covers Brickoo\Core\Application::getModules
         * @todo Implement testGetModules().
         */
        public function testGetModules()
        {
            // Remove the following lines when you implement this test.
            $this->markTestIncomplete(
              'This test has not been implemented yet.'
            );
        }

        /**
         * @covers Brickoo\Core\Application::isModuleAvailable
         * @todo Implement testIsModuleAvailable().
         */
        public function testIsModuleAvailable()
        {
            // Remove the following lines when you implement this test.
            $this->markTestIncomplete(
              'This test has not been implemented yet.'
            );
        }

        /**
         * @covers Brickoo\Core\Application::getModulePath
         * @todo Implement testGetModulePath().
         */
        public function testGetModulePath()
        {
            // Remove the following lines when you implement this test.
            $this->markTestIncomplete(
              'This test has not been implemented yet.'
            );
        }

        /**
         * @covers Brickoo\Core\Application::registerDirectory
         * @todo Implement testRegisterDirectory().
         */
        public function testRegisterDirectory()
        {
            // Remove the following lines when you implement this test.
            $this->markTestIncomplete(
              'This test has not been implemented yet.'
            );
        }

        /**
         * @covers Brickoo\Core\Application::registerPublicDirectory
         * @todo Implement testRegisterPublicDirectory().
         */
        public function testRegisterPublicDirectory()
        {
            // Remove the following lines when you implement this test.
            $this->markTestIncomplete(
              'This test has not been implemented yet.'
            );
        }

        /**
         * @covers Brickoo\Core\Application::hasPublicDirectory
         * @todo Implement testHasPublicDirectory().
         */
        public function testHasPublicDirectory()
        {
            // Remove the following lines when you implement this test.
            $this->markTestIncomplete(
              'This test has not been implemented yet.'
            );
        }

        /**
         * @covers Brickoo\Core\Application::has
         * @todo Implement testHas().
         */
        public function testHas()
        {
            // Remove the following lines when you implement this test.
            $this->markTestIncomplete(
              'This test has not been implemented yet.'
            );
        }

        /**
         * @covers Brickoo\Core\Application::get
         * @todo Implement testGet().
         */
        public function testGet()
        {
            // Remove the following lines when you implement this test.
            $this->markTestIncomplete(
              'This test has not been implemented yet.'
            );
        }

        /**
         * @covers Brickoo\Core\Application::set
         * @todo Implement testSet().
         */
        public function testSet()
        {
            // Remove the following lines when you implement this test.
            $this->markTestIncomplete(
              'This test has not been implemented yet.'
            );
        }

        /**
         * @covers Brickoo\Core\Application::__get
         * @todo Implement test__get().
         */
        public function test__get()
        {
            // Remove the following lines when you implement this test.
            $this->markTestIncomplete(
              'This test has not been implemented yet.'
            );
        }

        /**
         * @covers Brickoo\Core\Application::__set
         * @todo Implement test__set().
         */
        public function test__set()
        {
            // Remove the following lines when you implement this test.
            $this->markTestIncomplete(
              'This test has not been implemented yet.'
            );
        }

        /**
         * @covers Brickoo\Core\Application::__isset
         * @todo Implement test__isset().
         */
        public function test__isset()
        {
            // Remove the following lines when you implement this test.
            $this->markTestIncomplete(
              'This test has not been implemented yet.'
            );
        }

        /**
         * @covers Brickoo\Core\Application::run
         * @todo Implement testRun().
         */
        public function testRun()
        {
            // Remove the following lines when you implement this test.
            $this->markTestIncomplete(
              'This test has not been implemented yet.'
            );
        }

    }
