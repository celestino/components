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

    namespace Tests\Brickoo\Loader;

    require_once "Fixture/Autoloader.php";

    use Tests\Brickoo\Loader\Fixture\AutoloaderConcrete;

    /**
     * Test suite for the Autoloader class.
     * @see Brickoo\Loader\Autoloader
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class AutoloaderTest extends \PHPUnit_Framework_TestCase {

        /**
         * @covers Brickoo\Loader\Autoloader::__construct
         */
        public function testConstructor() {
            $Autoloader = new AutoloaderConcrete();
            $this->assertInstanceOf('\Brickoo\Loader\Autoloader', $Autoloader);
            $this->assertAttributeEquals(false, 'isRegistered', $Autoloader);
        }

        /**
         * @covers Brickoo\Loader\Autoloader::register
         */
        public function testRegisterAutoloader() {
            $Autoloader = new AutoloaderConcrete();
            $this->assertSame($Autoloader, $Autoloader->register());
            $this->assertAttributeEquals(true, 'isRegistered', $Autoloader);
            $this->assertTrue(spl_autoload_unregister(array($Autoloader, 'load')));
        }

        /**
         * Test if the registering of the same autloader throws an exception.
         * @covers Brickoo\Loader\Autoloader::register
         * @covers Brickoo\Loader\Exceptions\DuplicateAutoloaderRegistration
         * @expectedException Brickoo\Loader\Exceptions\DuplicateAutoloaderRegistration
         */
        public function testRegistrationThrowsDuplicateAutoloaderRegistrationException() {
            $Autoloader = new AutoloaderConcrete();
            try{
                $Autoloader->register();
                $Autoloader->register();
            }
            catch (\Brickoo\Loader\Exceptions\DuplicateAutoloaderRegistration $Exception) {
                $this->assertTrue(spl_autoload_unregister(array($Autoloader, 'load')));
                throw $Exception;
            }
        }

        /**
         * @covers Brickoo\Loader\Autoloader::unregister
         */
        public function testUnregisterAutoloader() {
            $Autoloader = new AutoloaderConcrete();
            $Autoloader->register();
            $this->assertAttributeEquals(true, 'isRegistered', $Autoloader);
            $this->assertSame($Autoloader, $Autoloader->unregister());
            $this->assertAttributeEquals(false, 'isRegistered', $Autoloader);
            $this->assertFalse(spl_autoload_unregister(array($Autoloader, 'load')));
        }

        /**
         * @covers Brickoo\Loader\Autoloader::unregister
         * @covers Brickoo\Loader\Exceptions\AutoloaderNotRegistered
         * @expectedException Brickoo\Loader\Exceptions\AutoloaderNotRegistered
         */
        public function testUnregisterThrowsAutoloaderNotRegisteredExeption() {
            $Autoloader = new AutoloaderConcrete();
            try {
                $Autoloader->unregister();
            }
            catch (\Brickoo\Loader\Exceptions\AutoloaderNotRegistered $Exception) {
                $this->assertFalse(spl_autoload_unregister(array($Autoloader, 'load')));
                throw $Exception;
            }
        }

        /**
         * @covers Brickoo\Loader\Autoloader::load
         */
        public function testAbstractLoadDummy() {
            $Autoloader = new AutoloaderConcrete();
            $this->assertNull($Autoloader->load('not implemented'));
        }

     }