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

    use Brickoo\Memory\Locker;

    // require PHPUnit Autoloader
    require_once ('PHPUnit/Autoload.php');

    /**
     * LockerTest
     *
     * Test suite for the Locker class.
     * @see Brickoo\Memory\Locker
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class LockerTest extends PHPUnit_Framework_TestCase {

        /**
         * Holds an instance of the LockerTestable class.
         * @var \Brickoo\Memory\Locker
         */
        public $LockerTestable;

        /**
         * Set up the LockerTestable object used.
         * @return void
         */
        public function setUp() {
            $this->LockerTestable = new LockerTestable();
        }

        /**
         * Test if the class can be created.
         * @covers Brickoo\Memory\Locker::__construct
         */
        public function testLockerConstructor() {
            $this->assertInstanceOf('\Brickoo\Memory\Locker', ($Locker = new LockerTestable()));
        }

        /**
         * Test if an identifier can be locked and returns a unlock key.
         * Test if the indentifier can be unlocked with the returned key.
         * @covers Brickoo\Memory\Locker::lock
         * @covers Brickoo\Memory\Locker::unlock
         * @covers Brickoo\Memory\Locker::isLocked
         */
        public function testLockAndUnlockRoutine() {
            $this->assertInternalType('string', ($unlockKey = $this->LockerTestable->lock('name')));
            $this->assertTrue($this->LockerTestable->isLocked('name'));
            $this->assertSame($this->LockerTestable, $this->LockerTestable->unlock('name', $unlockKey));
            $this->assertFalse($this->LockerTestable->isLocked('name'));
        }

        /**
         * Test if trying to use a wrong argument type throws an exception.
         * @covers Brickoo\Memory\Locker::lock
         * @expectedException InvalidArgumentException
         */
        public function testLockArgumentException() {
            $this->LockerTestable->lock(array('wrongType'));
        }

        /**
         * Test if trying to lock a privous locked identifier throws an exception.
         * @covers Brickoo\Memory\Locker::lock
         * @covers Brickoo\Memory\Exceptions\LockFailedException::__construct
         * @expectedException  Brickoo\Memory\Exceptions\LockFailedException
         */
        public function testLockFailedException() {
            $this->LockerTestable->lock('name');
            $this->LockerTestable->lock('name');
        }

        /**
         * Test if trying to use a wrong unlock key throws an exception.
         * @covers Brickoo\Memory\Locker::unlock
         * @covers Brickoo\Memory\Exceptions\UnlockFailedException::__construct
         * @expectedException Brickoo\Memory\Exceptions\UnlockFailedException
         */
        public function testUnLockWrongUnlockKeyException() {
            $this->LockerTestable->lock('name');
            $this->LockerTestable->unlock('name', 'invalidKey');
        }

        /**
         * Test if the unvalid lock identifier throws an exception.
         * @covers Brickoo\Memory\Locker::unlock
         * @expectedException  InvalidArgumentException
         */
        public function testUnlockInvalidArgumentException() {
            $this->LockerTestable->unlock(array('wrongType'), 'invalidKey');
        }

        /**
         * Test if not passed identifiers throws an exception.
         * @covers Brickoo\Memory\Locker::unlock
         * @covers Brickoo\Memory\Exceptions\UnlockFailedException
         * @expectedException Brickoo\Memory\Exceptions\UnlockFailedException
         */
        public function testUnlockFailedException() {
            $this->LockerTestable->unlock('notLocked', 'invalidKey');
        }

        /**
         * Test if not passed identifiers throws an exception.
         * @covers Brickoo\Memory\Locker::isLocked
         * @expectedException InvalidArgumentException
         */
        public function testIsLockedArgumentException() {
            $this->LockerTestable->isLocked(array('wrongType'));
        }

        /**
         * Test for the magic count method implemented by the \Countable interface.
         * @covers Brickoo\Memory\Locker::count
         */
        public function testCount() {
            $this->LockerTestable->lock('name');
            $this->LockerTestable->lock('town');
            $this->LockerTestable->lock('country');
            $this->assertEquals(3, count($this->LockerTestable));
        }

    }

    class LockerTestable extends Locker {
        /**
         * Abstract method to check if the main class has the identifier.
         * @param string|integer $identifier the identifier to check
         * @see Brickoo\Memory\Locker::isIdentifierAvailable()
         * @return boolean true
         */
        public function isIdentifierAvailable($identifier) {
            return true;
        }

    }