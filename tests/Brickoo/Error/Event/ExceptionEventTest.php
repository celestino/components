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

namespace Brickoo\Tests\Error;

use Brickoo\Error\Events,
    Brickoo\Error\Event\ExceptionEvent,
    PHPUnit_Framework_TestCase;

/**
 * ExceptionEventTest
 *
 * Test suite for the ExceptionEvent class.
 * @see Brickoo\Error\Event\ExceptionEvent
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class ExceptionEventTest extends PHPUnit_Framework_TestCase {

    /** @covers Brickoo\Error\Event\ExceptionEvent::__construct */
    public function testContructor() {
        $exception = new \Exception("Some test exception.");
        $expectedParameters =[ExceptionEvent::PARAM_EXCEPTION => $exception];

        $exceptionEvent = new ExceptionEvent($exception);
        $this->assertInstanceOf("\\Brickoo\\Event\\Event", $exceptionEvent);
        $this->assertAttributeEquals(Events::EXCEPTION, "name", $exceptionEvent);
        $this->assertAttributeEquals($expectedParameters, "params", $exceptionEvent);
    }

    /**
     * @covers Brickoo\Error\Event\ExceptionEvent::getException
     */
    public function testGetException() {
        $exception = new \Exception("Some test exception.");
        $exceptionEvent = new ExceptionEvent($exception);
        $this->assertSame($exception, $exceptionEvent->getException());
    }

}