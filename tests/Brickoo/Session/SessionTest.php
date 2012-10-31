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

    namespace Tests\Brickoo\Session;

    use Brickoo\Session\Session;

    /**
     * SessionTest
     *
     * Test suite for the Session class.
     * Using the Session the session.autostart configuration should be set to zero.
     * @see Brickoo\Session\Session
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class SessionTest extends \PHPUnit_Framework_TestCase {

        /**
         * Holds an instance of the test Session class.
         * @var \Brickoo\Session\Session
         */
        private $Session;

        /**
         * {@inheritDoc}
         * Prevent sending any headers since it would produce
         * an error with the PHPUnit previous output.
         * @return void
         */
        public function setUp() {
            ini_set("session.use_cookies", false);
            ini_set("session.cache_limiter", "");
            $this->Session = new Session();
        }

        /**
         * {@inheritDoc}
         * Checks if the session has been started and stops if required.
         * @return void
         */
        public function tearDown() {
            if ($this->Session->hasStarted()) {
                $this->Session->stop();
            }
        }

        /**
         * @covers Brickoo\Session\Session::registerHandler
         * @covers Brickoo\Session\Session::checkSessionStart
         */
        public function testRegisterSessionHandler() {
            $Handler = $this->getMock('Brickoo\Session\Interfaces\Handler');

            $Session = $this->Session;
            $this->assertSame($Session, $Session->registerHandler($Handler));
        }

        /**
         * @covers Brickoo\Session\Session::registerHandler
         * @expectedException InvalidArgumentException
         */
        public function testRegisterSessionHandlerThrowsInvalidArgumentException() {
            $Session = $this->Session;
            $Session->registerHandler(new \stdClass());
        }

        /**
         * @covers Brickoo\Session\Session::getId
         * @covers Brickoo\Session\Session::setId
         * @covers Brickoo\Session\Session::checkSessionStart
         */
        public function testSessionIdRoutines() {
            $sessionId = "testcase12345";
            $Session = $this->Session;

            $Session->setId($sessionId);
            $this->assertEquals($sessionId, $Session->getId());
        }

        /**
         * @covers Brickoo\Session\Session::regenerateId
         */
        public function testRegenerateId() {
            $Session = $this->Session;

            $Session->start();
            $sessionId = $Session->getId();
            $this->assertSame($Session, $Session->regenerateId());
            $this->assertNotEquals($sessionId, $Session->getId());
        }

        /**
         * @covers Brickoo\Session\Session::getName
         * @covers Brickoo\Session\Session::setName
         * @covers Brickoo\Session\Session::checkSessionStart
         */
        public function testSessionNameRoutines() {
            $sessionName = "testCaseSession";
            $Session = $this->Session;

            $Session->setName($sessionName);
            $this->assertEquals($sessionName, $Session->getName());
        }

        /**
         * @covers Brickoo\Session\Session::getCookieParams
         */
        public function testGetCookieParams() {
            $Session = $this->Session;
            $this->assertInternalType("array", ($cookie = $Session->getCookieParams()));
        }

        /**
         * @covers Brickoo\Session\Session::setCookieParams
         * @covers Brickoo\Session\Session::checkSessionStart
         */
        public function testSetCookieParameters() {
            ini_set("session.use_cookies", true);

            $lifetime = 3600;
            $path = "/somePath";
            $domain = "session-testcase.localhost";
            $secure = true;
            $httponly = true;

            $expectedCookieParams = array(
                "lifetime" => $lifetime,
                "path" => $path,
                "domain" => $domain,
                "secure" => $secure,
                "httponly" => $httponly
            );

            $Session = $this->Session;
            $this->assertSame($Session, $Session->setCookieParams($lifetime, $path, $domain, $secure, $httponly));
            $this->assertEquals(session_get_cookie_params(), $expectedCookieParams);
        }

        /**
         * @covers Brickoo\Session\Session::getCacheLimiter
         * @covers Brickoo\Session\Session::setCacheLimiter
         */
        public function testCacheLimiterRoutines() {
            $cacheLImiter = "testCase";
            $Session = $this->Session;

            $this->assertSame($Session, $Session->setCacheLimiter($cacheLImiter));
            $this->assertEquals($cacheLImiter, $Session->getCacheLimiter());
        }

        /**
         * @covers Brickoo\Session\Session::start
         * @covers Brickoo\Session\Session::stop
         * @covers Brickoo\Session\Session::checkSessionStart
         */
        public function testStartAndStopSession() {
            $Session = $this->Session;

            $Session->start();
            $this->assertAttributeEquals(true, "SessionStarted", $Session);
            $Session->stop();
            $this->assertAttributeEquals(null, "SessionStarted", $Session);
        }

        /**
         * @covers Brickoo\Session\Session::start
         * @covers Brickoo\Session\Session::checkSessionStart
         * @covers Brickoo\Session\Exceptions\SessionAlreadyStarted
         * @expectedException Brickoo\Session\Exceptions\SessionAlreadyStarted
         */
        public function testStartSessionTwiceThrowsException() {
            $Session = $this->Session;
            $Session->start();
            $Session->start();
        }

        /**
         * @covers Brickoo\Session\Session::hasStarted
         */
        public function testSessionHasStarted() {
            $Session = $this->Session;

            $this->assertFalse($Session->hasStarted());
            $Session->start();
            $this->assertTrue($Session->hasStarted());
            $Session->stop();
            $this->assertFalse($Session->hasStarted());
        }

        /**
         * @covers Brickoo\Session\Session::destroy
         * @covers Brickoo\Session\Session::isCookieUsed
         */
        public function testDestroy() {

            $_SESSION["test"] = "case";
            $callback = function(){};
            $Session = $this->Session;

            $Session->start();
            ini_set("session.use_cookies", true);
            $this->assertSame($Session, $Session->destroy($callback));
            $this->assertFalse(isset($_SESSION["test"]));
        }

    }