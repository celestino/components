<?php

    /*
     * Copyright (c) 2011-2013, Celestino Diaz <celestino.diaz@gmx.de>.
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

    namespace Tests\Brickoo\Memory;

    use Brickoo\Memory\Locker;

    /**
     * LockerTest
     *
     * Test suite for the Locker class.
     * @see Brickoo\Memory\Locker
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class LockerTest extends \PHPUnit_Framework_TestCase {

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
         * @covers Brickoo\Memory\Locker::__construct
         */
        public function testConstructor() {
            $this->assertAttributeInternalType("array", "locked", $this->LockerTestable);
        }

        /**
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
         * @covers Brickoo\Memory\Locker::lock
         * @expectedException InvalidArgumentException
         */
        public function testLockArgumentException() {
            $this->LockerTestable->lock(array('wrongType'));
        }

        /**
         * @covers Brickoo\Memory\Locker::lock
         * @covers Brickoo\Memory\Exceptions\LockFailedException::__construct
         * @expectedException  Brickoo\Memory\Exceptions\LockFailedException
         */
        public function testLockFailedException() {
            $this->LockerTestable->lock('name');
            $this->LockerTestable->lock('name');
        }

        /**
         * @covers Brickoo\Memory\Locker::unlock
         * @covers Brickoo\Memory\Exceptions\UnlockFailedException::__construct
         * @expectedException Brickoo\Memory\Exceptions\UnlockFailedException
         */
        public function testUnLockWrongUnlockKeyException() {
            $this->LockerTestable->lock('name');
            $this->LockerTestable->unlock('name', 'invalidKey');
        }

        /**
         * @covers Brickoo\Memory\Locker::unlock
         * @expectedException  InvalidArgumentException
         */
        public function testUnlockInvalidArgumentException() {
            $this->LockerTestable->unlock(array('wrongType'), 'invalidKey');
        }

        /**
         * @covers Brickoo\Memory\Locker::unlock
         * @covers Brickoo\Memory\Exceptions\UnlockFailedException
         * @expectedException Brickoo\Memory\Exceptions\UnlockFailedException
         */
        public function testUnlockFailedException() {
            $this->LockerTestable->unlock('notLocked', 'invalidKey');
        }

        /**
         * @covers Brickoo\Memory\Locker::isLocked
         * @expectedException InvalidArgumentException
         */
        public function testIsLockedArgumentException() {
            $this->LockerTestable->isLocked(array('wrongType'));
        }

        /**
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