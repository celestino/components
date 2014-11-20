<?php

/*
 * Copyright (c) 2011-2014, Celestino Diaz <celestino.diaz@gmx.de>
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

namespace Brickoo\Component\Session;

use Brickoo\Component\Session\Exception\SessionAlreadyStartedException;
use Brickoo\Component\Validation\Argument;

/**
 * SessionManager
 *
 * Object wrapper for the PHP session handling.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class SessionManager {

    /** @var boolean */
    protected static $sessionStarted;

    /**
     * Returns the current session identifier.
     * @return string the session identifier
     */
    public function getId() {
        return session_id();
    }

    /**
     * Set the session identifier.
     * @param string $identifier the session identifier
     * @throws \InvalidArgumentException if the argument is not valid
     * @throws \Brickoo\Component\Session\Exception\SessionAlreadyStartedException
     * @return string the previously used session identifier
     */
    public function setId($identifier) {
        Argument::isString($identifier);
        $this->checkSessionStart();
        return session_id($identifier);
    }

    /**
     * Regenerates the session id.
     * The values of the current session should be kept.
     * @return \Brickoo\Component\Session\SessionManager
     */
    public function regenerateId() {
        session_regenerate_id(false);
        return $this;
    }

    /**
     * Returns the current session name.
     * @return string the current session name
     */
    public function getName() {
        return session_name();
    }

    /**
     * Sets the session name.
     * @param string $name the session name
     * @throws \InvalidArgumentException if the argument is not valid
     * @throws \Brickoo\Component\Session\Exception\SessionAlreadyStartedException
     * @return string the previously used session name
     */
    public function setName($name) {
        Argument::isString($name);
        $this->checkSessionStart();
        return session_name($name);
    }

    /**
     * Returns the current session cookie parameters.
     * @return array the cookie parameters
     */
    public function getCookieParams() {
        return session_get_cookie_params();
    }

    /**
     * Sets the session cookie parameters.
     * @param integer $lifetime the cookie/session lifetime
     * @param string $path the request path to listen to
     * @param string $domain the domain to listen to
     * @param boolean $secure only should be sent while on https mode
     * @param boolean $httpOnly restriction to the http protocol
     * @throws \InvalidArgumentException if an argument is not valid
     * @throws \Brickoo\Component\Session\Exception\SessionAlreadyStartedException
     * @return \Brickoo\Component\Session\SessionManager
     */
    public function setCookieParams($lifetime, $path, $domain, $secure = false, $httpOnly = false) {
        Argument::isInteger($lifetime);
        Argument::isString($path);
        Argument::isString($domain);
        Argument::isBoolean($secure);
        Argument::isBoolean($httpOnly);

        $this->checkSessionStart();
        session_set_cookie_params($lifetime, $path, $domain, $secure, $httpOnly);
        return $this;
    }

    /**
     * Returns the current session cache limiter.
     * @return string the current used session cache limiter
     */
    public function getCacheLimiter() {
        return session_cache_limiter();
    }

    /**
     * Sets the session cache limiter.
     * @see session_cache_limiter() for possible values
     * @param string $limiter the cache limiter
     * @throws \InvalidArgumentException if the argument is not valid
     * @return \Brickoo\Component\Session\SessionManager
     */
    public function setCacheLimiter($limiter) {
        Argument::isString($limiter);
        session_cache_limiter($limiter);
        return $this;
    }

    /**
     * Starts the session.
     * @throws \Brickoo\Component\Session\Exception\SessionAlreadyStartedException
     * @return \Brickoo\Component\Session\SessionManager
     */
    public function start() {
        $this->checkSessionStart();

        if (session_start()) {
            self::$sessionStarted = true;
        }
        return $this;
    }

    /**
     * Checks if the session has already been started.
     * @return boolean check result
     */
    public function hasStarted() {
        return self::$sessionStarted === true;
    }

    /**
     * Stops the session by writing the current session values.
     * @return \Brickoo\Component\Session\SessionManager
     */
    public function stop() {
        session_write_close();
        self::$sessionStarted = null;
        return $this;
    }

    /**
     * Destroys the current session.
     * To prevent unexpected behaviours
     * the global $_SESSION variable should be cleared,
     * also the cookie (if used) should be expired.
     * This method is commonly used when logging-out an user.
     * @param null|callable $callback
     * @return \Brickoo\Component\Session\SessionManager
     */
    public function destroy($callback = null) {
        if ($this->isCookieUsed()) {
            $callback = is_callable($callback) ? $callback : "setcookie";
            $params = session_get_cookie_params();
            call_user_func_array($callback, [
                session_name(), "", time() - (365 * 24 * 60 * 60),
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            ]);
        }
        $_SESSION = [];
        session_destroy();

        return $this;
    }

    /**
     * Checks if the session has been started.
     * If the session has been started something
     * is going wrong, throws an exception
     * @throws \Brickoo\Component\Session\Exception\SessionAlreadyStartedException
     * @return void
     */
    private function checkSessionStart() {
        if ($this->hasStarted()) {
            throw new SessionAlreadyStartedException();
        }
    }

    /**
     * Checks if session cookies are used.
     * @return boolean check result
     */
    private function isCookieUsed() {
        return (boolean)ini_get("session.use_cookies");
    }

}
