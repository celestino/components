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

    use Brickoo\Http\Session\SessionManager;

    // require PHPUnit Autoloader
    require_once ('PHPUnit/Autoload.php');

    /**
     * SessionManagerTest
     *
     * Test suite for the SessionManager class.
     * Using the SessionManager the session.autostart configuration should be set to zero.
     * @see Brickoo\Http\Session\SessionManager
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class SessionManagerTest extends PHPUnit_Framework_TestCase
    {

        /**
         * Returns a SessionHandler stub implementig the Session\Interfaces\SessionHandlerInterface.
         * @param array $methods the methods to mock
         * @return object
         */
        public function getSessionHandlerStub()
        {
            return $this->getMock
            (
                'Brickoo\Http\Session\Handler\Interfaces\SessionHandlerInterface',
                array('setLifetime', 'open', 'read', 'write', 'destroy', 'close', 'gc')
            );
        }

        /**
         * Disable the use of cookies since it would produce
         * an error with the PHPUnit previous output made.
         * @return void
         */
        public function setUp()
        {
            ini_set('session.use_cookies', false);
        }

        /**
         * Test if the SessionManager can be created and implements the SessionManagerInterface.
         * @covers Brickoo\Http\Session\SessionManager::__construct
         * @covers Brickoo\Http\Session\SessionManager::registerSessionHandler
         */
        public function testConstruct()
        {
            $SessionHandlerStub = $this->getSessionHandlerStub();
            $SessionHandlerStub->expects($this->once())
                               ->method('setLifetime')
                               ->will($this->returnSelf());

            $this->assertInstanceOf('Brickoo\Http\Session\Interfaces\SessionManagerInterface',
                new SessionManager($SessionHandlerStub)
            );
        }

        /**
         * Test if the session cookie parameters can be overwriten and the session handler lifetime is updated.
         * Enable session cookies to test if the parameters did be overwriten.
         * @covers Brickoo\Http\Session\SessionManager::setCookieParameters
         */
        public function testSetCookieParameters()
        {
            ini_set('session.use_cookies', true);

            $cookieParameters = array
            (
                'lifetime'    => 3600,
                'domain'      => 'somedomain.de',
                'path'        => '/somePath',
                'httponly'    => true,
                'secure'      => true
            );

            $SessionHandlerStub = $this->getSessionHandlerStub();
            $SessionHandlerStub->expects($this->exactly(2))
                               ->method('setLifetime')
                               ->will($this->returnSelf());

            $SessionManager = new SessionManager($SessionHandlerStub);

            $this->assertEquals($cookieParameters, $SessionManager->setCookieParameters($cookieParameters));
            $this->assertEquals(session_get_cookie_params(), $cookieParameters);
        }

        /**
         * Test if the session configuration can be overwriten an the SessionManager reference is returned.
         * @covers Brickoo\Http\Session\SessionManager::configureSession
         */
        public function testConfigureSession()
        {
            $configuration = array
            (
                'name'        => 'session_name',
                'limiter'     => 'private',
            );

            $SessionManager = new SessionManager($this->getSessionHandlerStub());

            $this->assertSame($SessionManager, $SessionManager->configureSession(
                $configuration['name'], $configuration['limiter']
            ));
            $this->assertEquals(session_name(), $configuration['name']);
            $this->assertEquals(session_cache_limiter(), $configuration['limiter']);
        }

        /**
         * Test if a session  can be started and stopped and the start flag is updated.
         * @covers Brickoo\Http\Session\SessionManager::start
         * @covers Brickoo\Http\Session\SessionManager::stop
         * @covers Brickoo\Http\Session\SessionManager::hasSessionStarted
         */
        public function testStartAndStop()
        {
            $SessionHandlerStub = $this->getSessionHandlerStub();
            $SessionHandlerStub->expects($this->once())
                               ->method('setLifetime')
                               ->will($this->returnSelf());
            $SessionHandlerStub->expects($this->once())
                               ->method('open')
                               ->will($this->returnValue(true));
            $SessionHandlerStub->expects($this->once())
                               ->method('read')
                               ->will($this->returnValue(false));
            $SessionHandlerStub->expects($this->once())
                               ->method('write')
                               ->will($this->returnValue(true));
            $SessionHandlerStub->expects($this->once())
                               ->method('close')
                               ->will($this->returnValue(true));

            $SessionManager = new SessionManager($SessionHandlerStub);

            $SessionManager->start();
            $this->assertTrue($SessionManager->hasSessionStarted());

            $SessionManager->stop();
            $this->assertFalse($SessionManager->hasSessionStarted());
        }

    }