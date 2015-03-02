<?php

/*
 * Copyright (c) 2011-2015, Celestino Diaz <celestino.diaz@gmx.de>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace Brickoo\Tests\Component\Session;

use Brickoo\Component\Session\SessionManager;
use PHPUnit_Framework_TestCase;

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
        $this->assertInternalType("array", $this->sessionManager->getCookieParams());
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

        $expectedCookieParams = [
            "lifetime" => $lifetime,
            "path" => $path,
            "domain" => $domain,
            "secure" => $secure,
            "httponly" => $httponly
        ];

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
        $this->assertAttributeEquals(true, "sessionStarted", $this->sessionManager);
        $this->sessionManager->stop();
        $this->assertAttributeEquals(null, "sessionStarted", $this->sessionManager);
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
