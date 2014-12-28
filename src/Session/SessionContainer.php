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

namespace Brickoo\Component\Session;

use Brickoo\Component\Validation\Argument;

/**
 * SessionContainer
 *
 * Implements a session object based on namespaces which should prevent naming conflicts.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class SessionContainer implements \Countable, \IteratorAggregate{

    /** @var string */
    protected $sessionNamespace;

    /**
     * Class constructor.
     * @param string $sessionNamespace the namespace to use
     */
    public function __construct($sessionNamespace) {
        Argument::isString($sessionNamespace);
        $this->sessionNamespace = $sessionNamespace;
    }

    /**
    * Checks if the session property is available.
    * @param string $property the property to check in the session
    * @throws \InvalidArgumentException
    * @return boolean check result
    */
    public function contains($property) {
        Argument::isString($property);
        return isset($_SESSION[$this->getNamespace($property)]);
    }

    /**
     * Returns the session property hold content or the default value.
     * @param string $property the session property to retrieve the content from
     * @param mixed $defaultValue the default value if the property des not exist
     * @throws \InvalidArgumentException
     * @return mixed the property hold content or the default value if the property does not exist
     */
    public function get($property, $defaultValue = null) {
        Argument::isString($property);

        if (! $this->contains($property)) {
            return $defaultValue;
        }

        return $_SESSION[$this->getNamespace($property)];
    }

    /**
     * Sets the session property and assigns the content to it.
     * @param string $property the property to assign the content to
     * @param mixed $value the value to store
     * @throws \InvalidArgumentException
     * @return \Brickoo\Component\Session\SessionContainer
     */
    public function set($property, $value) {
        Argument::isString($property);
        $_SESSION[$this->getNamespace($property)] = $value;
        return $this;
    }

    /**
     * Removes the session property if available.
     * @param string $property the property to remove
     * @throws \InvalidArgumentException
     * @return \Brickoo\Component\Session\SessionContainer
     */
    public function remove($property) {
        Argument::isString($property);

        if ($this->contains($property)) {
            unset($_SESSION[$this->getNamespace($property)]);
        }

        return $this;
    }

    /**
     * Retrieve an external iterator
     * @link http://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return \ArrayIterator
     */
    public function getIterator() {
        return new \ArrayIterator($_SESSION);
    }

    /**
     * Count entries of the container
     * @link http://php.net/manual/en/countable.count.php
     * @return integer count of entries
     */
    public function count() {
        return count($_SESSION);
    }

    /**
     * Returns the property namespace name.
     * @param string $property the property to modify
     * @return string the session namespace of the property
     */
    private function getNamespace($property) {
        return $this->sessionNamespace.".".$property;
    }
}
