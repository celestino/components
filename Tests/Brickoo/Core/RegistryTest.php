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
     * 3. Neither the name of Application nor the names of its contributors may be used
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

    use Brickoo\Core\Registry;

    // require PHPUnit Autoloader
    require_once ('PHPUnit/Autoload.php');

    /**
     * Test suite for the Registry class.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class CoreRegistryTest extends \PHPUnit_Framework_TestCase
    {

        /**
         * Test if the Registry can be injected and the Core\Registry will be returned.
         * @covers Brickoo\Core\Registry::Registry
         */
        public function testInjectRegistry()
        {
            $Registry = new RegistryTestable();
            $RegistryMock = $this->getMock('Brickoo\Memory\Registry');
            $this->assertSame($Registry, $Registry->Registry($RegistryMock));
            $this->assertSame($RegistryMock, $Registry->Registry());
        }

        /**
         * Test if the Registry can be lazy initialized.
         * @covers Brickoo\Core\Registry::Registry
         */
        public function testRegistryLazyInitialization()
        {
            $Registry = new RegistryTestable();
            $this->assertInstanceOf('Brickoo\Memory\Registry', $Registry->Registry());
        }

        /**
         * Test if trying to overwrite the Registry dependency throws an exception.
         * @covers Brickoo\Core\Registry::Registry
         * @covers Brickoo\Core\Exceptions\DependencyOverwriteException::__construct
         * @expectedException Brickoo\Core\Exceptions\DependencyOverwriteException
         */
        public function testRegistryOverwriteException()
        {
            $Registry = new RegistryTestable();
            $RegistryMock = $this->getMock('Brickoo\Memory\Registry');
            $Registry->Registry($RegistryMock);
            $Registry->Registry($RegistryMock);
        }

        /**
         * Test registration routines.
         * @covers Brickoo\Core\Registry::get
         * @covers Brickoo\Core\Registry::register
         * @covers Brickoo\Core\Registry::isRegistered
         */
        public function testRegistrationRoutines()
        {
            $Registry = new RegistryTestable();

            $RegistryStub = $this->getMock(
                'Brickoo\Memory\Registry',
                array('register', 'isRegistered', 'get', 'lock')
            );
            $RegistryStub->expects($this->once())
                         ->method('register')
                         ->with('phpunit', 'test')
                         ->will($this->returnSelf());
            $RegistryStub->expects($this->once())
                         ->method('lock')
                         ->with('phpunit')
                         ->will($this->returnSelf());
            $RegistryStub->expects($this->once())
                         ->method('isRegistered')
                         ->with('phpunit')
                         ->will($this->returnValue(true));
            $RegistryStub->expects($this->once())
                         ->method('get')
                         ->with('phpunit')
                         ->will($this->returnValue('test'));
            $Registry->Registry($RegistryStub);

            $this->assertSame($Registry, $Registry->register('phpunit', 'test'));
            $this->assertTrue($Registry->isRegistered('phpunit'));
            $this->assertEquals('test', $Registry->get('phpunit'));
        }

    }

    class RegistryTestable extends Registry
    {
        public function __construct()
        {
            self::$_Registry = null;
        }
    }