<?php

    /*
     * Copyright (c) 2008-2011, Celestino Diaz Teran <celestino@users.sourceforge.net>.
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

    use Brickoo\Library\Storage\Locker;

    // require PHPUnit Autoloader
    require_once ('PHPUnit/Autoload.php');

    /**
     * LockerTest
     *
     * Test case for the Locker class.
     * @see Brickoo\Library\Storage\Locker
     * @author Celestino Diaz Teran <celestino@users.sourceforge.net>
     * @version $Id$
     */

    class LockerTest extends PHPUnit_Framework_TestCase
    {

        /**
         * Holds an instance of the LockerFixture class.
         * @var object LockerFixture
         */
        public $LockerFixture;

        /**
         * Set up the LockerFixture object used.
         * @return void
         */
        public function setUp()
        {
            $this->LockerFixture = new LockerFixture();
        }

        /**
         * Test if the class can be created.
         * @covers Brickoo\Library\Storage\Locker::__construct
         * @covers Brickoo\Library\Storage\Locker::clearLocker
         */
        public function testLockerConstructor()
        {
            $this->assertInstanceOf
            (
                '\Brickoo\Library\Storage\Locker',
                ($Locker = new LockerFixture())
            );
        }

        /**
         * Test if the identifier can be locked.
         * @covers Brickoo\Library\Storage\Locker::lock
         * @covers Brickoo\Library\Storage\Locker::cleanToLockIdentifiers
         */
        public function testLock()
        {
            $this->assertSame($this->LockerFixture, $this->LockerFixture->lock('name'));
            $this->assertSame($this->LockerFixture, $this->LockerFixture->lock(array('name', 'age', 'town')));
        }

        /**
         * Test if the unvalid lock identifier throws an exception.
         * @covers Brickoo\Library\Storage\Locker::lock
         * @expectedException  InvalidArgumentException
         */
        public function testLockInvalidArgumentException()
        {
            $this->LockerFixture->lock(new stdClass());
        }

        /**
         * Test if not passed identifiers throws an exception.
         * @covers Brickoo\Library\Storage\Locker::lock
         * @covers Brickoo\Library\Storage\Exceptions\LockFailedException
         * @expectedException  Brickoo\Library\Storage\Exceptions\LockFailedException
         */
        public function testLockFailedException()
        {
            $this->LockerFixture->lock(array());
        }

        /**
         * Test if the identifier can be unlocked.
         * @covers Brickoo\Library\Storage\Locker::unlock
         * @covers Brickoo\Library\Storage\Locker::cleanToUnlockIdentifiers
         */
        public function testUnlock()
        {
            $this->LockerFixture->lock('name');
            $this->assertSame($this->LockerFixture, $this->LockerFixture->unlock('name'));

            $this->LockerFixture->lock('name');
            $this->assertSame($this->LockerFixture, $this->LockerFixture->unlock(array('name')));

            $this->LockerFixture->lock('name');
            $this->assertSame($this->LockerFixture, $this->LockerFixture->unlock(array('name', 'identifierNotLocked')));
        }

        /**
         * Test if the unvalid lock identifier throws an exception.
         * @covers Brickoo\Library\Storage\Locker::unlock
         * @expectedException  InvalidArgumentException
         */
        public function testUnlockInvalidArgumentException()
        {
            $this->LockerFixture->unlock(new stdClass());
        }

        /**
         * Test if not passed identifiers throws an exception.
         * @covers Brickoo\Library\Storage\Locker::unlock
         * @covers Brickoo\Library\Storage\Exceptions\UnlockFailedException
         * @expectedException Brickoo\Library\Storage\Exceptions\UnlockFailedException
         */
        public function testUnlockFailedException()
        {
            $this->LockerFixture->unlock('notLocked');
        }

        /**
         * Test if the identifier is locked.
         * @covers Brickoo\Library\Storage\Locker::isLocked
         */
        public function testIsLocked()
        {
            $this->LockerFixture->lock('name');
            $this->assertTrue($this->LockerFixture->isLocked('name'));
            $this->assertFalse($this->LockerFixture->isLocked('notLocked'));
        }

        /**
         * Test if not passed identifiers throws an exception.
         * @covers Brickoo\Library\Storage\Locker::isLocked
         * @expectedException InvalidArgumentException
         */
        public function testIsLockerArgumentException()
        {
            $this->LockerFixture->isLocked(array('wrongType'));
        }

        /**
         * Test for the magic count method.
         * @covers Brickoo\Library\Storage\Locker::count
         */
        public function testCount()
        {
            $this->LockerFixture->lock('name');
            $this->LockerFixture->lock('town');
            $this->LockerFixture->lock('country');
            $this->assertEquals(3, count($this->LockerFixture));
        }

        /**
         * Test for the backup count method.
         * @covers Brickoo\Library\Storage\Locker::getAmountOfLockedIdentifiers
         */
        public function testGetAmountOfLockedIdentifiers()
        {
            $this->LockerFixture->lock('name');
            $this->LockerFixture->lock('town');
            $this->LockerFixture->lock('country');
            $this->assertEquals(3, $this->LockerFixture->getAmountOfLockedIdentifiers());
        }

    }

    /**
     * Fixture class for the abstract Locker class.
     */
    class LockerFixture extends Locker
    {
        /**
         * Abstract method to check if the main class has the identifier.
         * @param string|integer $identifier the extern identifier to check
         * @see Brickoo\Library\Storage\Locker::isIdentifierAvailable()
         * @return boolean true
         */
        public function isIdentifierAvailable($identifier)
        {
            return true;
        }

    }

?>