<?php

/*
 * Copyright (c) 2011-2014, Celestino Diaz <celestino.diaz@gmx.de>.
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

namespace Brickoo\Tests\Component\Session;

use Brickoo\Component\Session\SessionManager,
    PHPUnit_Framework_TestCase;

/**
 * SessionManagerTest
 *
 * Test suite for the SessionManager class.
 * Using the Session the session.autostart configuration should be set to zero.
 * @see Brickoo\Component\Session\SessionManager
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class SessionManagerTest extends PHPUnit_Framework_TestCase {

    /**
     * Holds an instance of the test Session class.
     * @var \Brickoo\Component\Session\SessionManager
     */
    private $sessionManager;

    /**
     * {@inheritDoc}
     * Prevent sending any headers since it would produce
     * an error with the PHPUnit previous output.
     * @return void
     */
    public function setUp() {
        ini_set("session.use_cookies", false);
        ini_set("session.cache_limiter", "");
        $this->sessionManager = new SessionManager();
    }

    /**
     * {@inheritDoc}
     * Checks if the session has been started and stops if required.
     * @return void
     */
    public function tearDown() {
        if ($this->sessionManager->hasStarted()) {
            $this->sessionManager->stop();
        }
    }

    /**
     * @runInSeparateProcess
     * @covers Brickoo\Component\Session\SessionManager::__construct
     */
    public function testConstructorSetSessionHandler() {
        $sessionManager = new SessionManager($this->getMock("\\SessionHandler"));
        $this->assertInstanceOf("\\Brickoo\\Component\\Session\\SessionManager", $sessionManager);
    }

    /**
     * @covers Brickoo\Component\Session\SessionManager::getId
     * @covers Brickoo\Component\Session\SessionManager::setId
     * @covers Brickoo\Component\Session\SessionManager::checkSessionStart
     */
    public function testSessionIdRoutines() {
        $sessionId = "testCase12345";
        $this->sessionManager->setId($sessionId);
        $this->assertEquals($sessionId, $this->sessionManager->getId());
    }

    /** @covers Brickoo\Component\Session\SessionManager::regenerateId */
    public function testRegenerateId() {
        $this->sessionManager->start();
        $sessionId = $this->sessionManager->getId();
        $this->assertSame($this->sessionManager, $this->sessionManager->regenerateId());
        $this->assertNotEquals($sessionId, $this->sessionManager->getId());
    }

    /**
     * @covers Brickoo\Component\Session\SessionManager::getName
     * @covers Brickoo\Component\Session\SessionManager::setName
     * @covers Brickoo\Component\Session\SessionManager::checkSessionStart
     */
    public function testSessionNameRoutines() {
        $sessionName = "testCaseSession";
        $this->sessionManager->setName($sessionName);
        $this->assertEquals($sessionName, $this->sessionManager->getName());
    }

    /** @covers Brickoo\Component\Session\SessionManager::getCookieParams */
    public function testGetCookieParams() {
        $this->assertInternalType("array", ($cookie = $this->sessionManager->getCookieParams()));
    }

    /**
     * @covers Brickoo\Component\Session\SessionManager::setCookieParams
     * @covers Brickoo\Component\Session\SessionManager::checkSessionStart
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

        $this->assertSame($this->sessionManager, $this->sessionManager->setCookieParams($lifetime, $path, $domain, $secure, $httponly));
        $this->assertEquals(session_get_cookie_params(), $expectedCookieParams);
    }

    /**
     * @covers Brickoo\Component\Session\SessionManager::getCacheLimiter
     * @covers Brickoo\Component\Session\SessionManager::setCacheLimiter
     */
    public function testCacheLimiterRoutines() {
        $cacheLimiter = "testCase";
        $this->assertSame($this->sessionManager, $this->sessionManager->setCacheLimiter($cacheLimiter));
        $this->assertEquals($cacheLimiter, $this->sessionManager->getCacheLimiter());
    }

    /**
     * @covers Brickoo\Component\Session\SessionManager::start
     * @covers Brickoo\Component\Session\SessionManager::stop
     * @covers Brickoo\Component\Session\SessionManager::checkSessionStart
     */
    public function testStartAndStopSession() {
        $this->sessionManager->start();
        $this->assertAttributeEquals(true, "SessionStarted", $this->sessionManager);
        $this->sessionManager->stop();
        $this->assertAttributeEquals(null, "SessionStarted", $this->sessionManager);
    }

    /**
     * @covers Brickoo\Component\Session\SessionManager::start
     * @covers Brickoo\Component\Session\SessionManager::checkSessionStart
     * @covers Brickoo\Component\Session\Exception\SessionAlreadyStartedException
     * @expectedException \Brickoo\Component\Session\Exception\SessionAlreadyStartedException
     */
    public function testStartSessionTwiceThrowsException() {
        $this->sessionManager->start();
        $this->sessionManager->start();
    }

    /** @covers Brickoo\Component\Session\SessionManager::hasStarted */
    public function testSessionHasStarted() {
        $this->assertFalse($this->sessionManager->hasStarted());
        $this->sessionManager->start();
        $this->assertTrue($this->sessionManager->hasStarted());
        $this->sessionManager->stop();
        $this->assertFalse($this->sessionManager->hasStarted());
    }

    /**
     * @covers Brickoo\Component\Session\SessionManager::destroy
     * @covers Brickoo\Component\Session\SessionManager::isCookieUsed
     */
    public function testDestroy() {
        $_SESSION["test"] = "case";
        $callback = function(){};
        $this->sessionManager->start();
        ini_set("session.use_cookies", true);
        $this->assertSame($this->sessionManager, $this->sessionManager->destroy($callback));
        $this->assertFalse(isset($_SESSION["test"]));
    }

}
