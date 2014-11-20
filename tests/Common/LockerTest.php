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

namespace Brickoo\Tests\Component\Common;

use Brickoo\Component\Common\Locker;
use PHPUnit_Framework_TestCase;

/**
 * LockerTest
 *
 * Test suite for the Locker class.
 * @see Brickoo\Component\Common\Locker
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class LockerTest extends PHPUnit_Framework_TestCase {

    /**
     * Holds an instance of the lockerTestable class.
     * @var \Brickoo\Component\Common\Locker
     */
    public $lockerTestable;

    /**
     * Set up the lockerTestable object used.
     * @return void
     */
    public function setUp() {
        $this->lockerTestable = new LockerTestable();
    }

    /**
     * @covers Brickoo\Component\Common\Locker::__construct
     * @covers Brickoo\Component\Common\Locker::lock
     * @covers Brickoo\Component\Common\Locker::unlock
     * @covers Brickoo\Component\Common\Locker::isLocked
     */
    public function testLockAndUnlockRoutine() {
        $this->assertInternalType("string", ($unlockKey = $this->lockerTestable->lock("name")));
        $this->assertTrue($this->lockerTestable->isLocked("name"));
        $this->assertSame($this->lockerTestable, $this->lockerTestable->unlock("name", $unlockKey));
        $this->assertFalse($this->lockerTestable->isLocked("name"));
    }

    /**
     * @covers Brickoo\Component\Common\Locker::lock
     * @expectedException \InvalidArgumentException
     */
    public function testLockArgumentException() {
        $this->lockerTestable->lock(array("wrongType"));
    }

    /**
     * @covers Brickoo\Component\Common\Locker::lock
     * @covers Brickoo\Component\Common\Exception\LockFailedException::__construct
     * @expectedException \Brickoo\Component\Common\Exception\LockFailedException
     */
    public function testLockFailedException() {
        $this->lockerTestable->lock("name");
        $this->lockerTestable->lock("name");
    }

    /**
     * @covers Brickoo\Component\Common\Locker::unlock
     * @covers Brickoo\Component\Common\Exception\UnlockFailedException::__construct
     * @expectedException \Brickoo\Component\Common\Exception\UnlockFailedException
     */
    public function testUnLockWrongUnlockKeyException() {
        $this->lockerTestable->lock("name");
        $this->lockerTestable->unlock("name", "invalidKey");
    }

    /**
     * @covers Brickoo\Component\Common\Locker::unlock
     * @expectedException \InvalidArgumentException
     */
    public function testUnlockInvalidArgumentException() {
        $this->lockerTestable->unlock(array("wrongType"), "invalidKey");
    }

    /**
     * @covers Brickoo\Component\Common\Locker::unlock
     * @covers Brickoo\Component\Common\Exception\UnlockFailedException
     * @expectedException \Brickoo\Component\Common\Exception\UnlockFailedException
     */
    public function testUnlockFailedException() {
        $this->lockerTestable->unlock("notLocked", "invalidKey");
    }

    /**
     * @covers Brickoo\Component\Common\Locker::isLocked
     * @expectedException \InvalidArgumentException
     */
    public function testIsLockedArgumentException() {
        $this->lockerTestable->isLocked(array("wrongType"));
    }

    /** @covers Brickoo\Component\Common\Locker::count */
    public function testCount() {
        $this->lockerTestable->lock("name");
        $this->lockerTestable->lock("town");
        $this->lockerTestable->lock("country");
        $this->assertEquals(3, count($this->lockerTestable));
    }

}

class LockerTestable extends Locker {

    /**
     * Abstract method to check if the main class has the identifier.
     * @param string|integer $identifier the identifier to check
     * @see Brickoo\Component\Common\Locker::isIdentifierAvailable()
     * @return boolean true
     */
    public function isIdentifierAvailable($identifier) {
        return true;
    }

}
