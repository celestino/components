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

    use Brickoo\Library\Core\Brickoo;
    use Brickoo\Library\Storage\Registry;

    // require PHPUnit Autoloader
    require_once ('PHPUnit/Autoload.php');

    /**
     * Test case for the Brickoo class.
     * @see Brickoo\Library\Core\BrickooObject
     * @author Celestino Diaz Teran <celestino@users.sourceforge.net>
     * @version $Id$
     */

    class BrickooTest extends PHPUnit_Framework_TestCase
    {

        /**
         * Holds an instance of the Brickoo object.
         * @var object
         */
        protected $Brickoo;

        /**
         * Set up the Brickoo object used.
         */
        public function setUp()
        {
            $this->Brickoo = new BrickooFixture();
        }

        /**
         * Test if setting the Registry returns the created Registry object.
         * @covers Brickoo\Library\Core\Brickoo::setRegistry
         */
        public function testSetRegistry()
        {
            $this->Brickoo->reset();

            $this->assertSame($this->Brickoo, $this->Brickoo->setRegistry());
        }

        /**
         * Test if initializing Brickoo returns the passed Registry object.
         * @covers Brickoo\Library\Core\Brickoo::setRegistry
         */
        public function testSetPassedRegistry()
        {
            $this->Brickoo->reset();

            $RegistryMock = $this->getMock('Brickoo\Library\Storage\Registry');
            $this->assertSame($this->Brickoo, $this->Brickoo->setRegistry($RegistryMock));
        }

        /**
         * Test if the reassigment of an Registry throws an exception.
         * @covers Brickoo\Library\Core\Brickoo::setRegistry
         * @expectedException Brickoo\Library\Core\Exceptions\DependencyOverrideException
         */
        public function testSetRegistryDependencyException()
        {
            $this->Brickoo->reset();

            $RegistryMock = $this->getMock('Brickoo\Library\Storage\Registry');

            $this->Brickoo->setRegistry();
            $this->Brickoo->setRegistry($RegistryMock);
        }

        /**
         * Test if the Registry reference is returned.
         * @covers Brickoo\Library\Core\Brickoo::getRegistry
         */
        public function testGetRegistry()
        {
            $this->Brickoo->reset();

            $this->assertInstanceOf
            (
                '\Brickoo\Library\Storage\Registry',
                $this->Brickoo->getRegistry()
            );
        }

    }

    /**
     * Fixture needed to reset the static Registry assigned.
     */
    class BrickooFixture extends Brickoo
    {
        /**
         * Resets the static Registry assigned.
         * @return void
         */
        public function reset()
        {
            static::$Registry = null;
        }
    }

?>