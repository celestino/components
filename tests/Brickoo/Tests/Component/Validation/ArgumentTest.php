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

namespace Brickoo\Tests\Component\Validation;

use Brickoo\Component\Validation\Argument,
    Brickoo\Component\Common\Container,
    PHPUnit_Framework_TestCase;

/**
 * ArgumentTest
 *
 * Test suite for the Argument class.
 * @see Brickoo\Component\Validation\Argument
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class ArgumentTest extends PHPUnit_Framework_TestCase {

    /** @covers Brickoo\Component\Validation\Argument::IsString */
    public function testIsString() {
        $this->assertTrue(Argument::IsString("test"));
    }

    /**
     * @covers Brickoo\Component\Validation\Argument::IsString
     * @expectedException \InvalidArgumentException
     */
    public function testIsStringThrowsInvalidArgumentException() {
        Argument::IsString(new \stdClass());
    }

    /** @covers Brickoo\Component\Validation\Argument::IsInteger */
    public function testIsInteger() {
        $this->assertTrue(Argument::IsInteger(1234));
        $this->assertTrue(Argument::IsInteger(0));
    }

    /**
     * @covers Brickoo\Component\Validation\Argument::IsInteger
     * @expectedException \InvalidArgumentException
     */
    public function testIsIntegerThrowsInvalidArgumentException() {
        Argument::IsInteger(new \stdClass());
    }

    /** @covers Brickoo\Component\Validation\Argument::IsStringOrInteger */
    public function testIsStringOrInteger() {
        $this->assertTrue(Argument::IsStringOrInteger(''));
        $this->assertTrue(Argument::IsStringOrInteger(0));
    }

    /**
     * @covers Brickoo\Component\Validation\Argument::IsStringOrInteger
     * @expectedException \InvalidArgumentException
     */
    public function testIsStringOrIntegerThrowsInvalidArgumentException() {
        Argument::IsStringOrInteger(array("wrongType"));
    }

    /** @covers Brickoo\Component\Validation\Argument::IsBoolean */
    public function testIsBoolean() {
        $this->assertTrue(Argument::IsBoolean(true));
        $this->assertTrue(Argument::IsBoolean(false));
    }

    /**
     * @covers Brickoo\Component\Validation\Argument::IsBoolean
     * @expectedException \InvalidArgumentException
     */
    public function testIsBooleanThrowsInvalidArgumentException() {
        Argument::IsBoolean(null);
    }

    /** @covers Brickoo\Component\Validation\Argument::IsFloat */
    public function testIsFloat() {
        $this->assertTrue(Argument::IsFloat(1.234));
    }

    /**
     * @covers Brickoo\Component\Validation\Argument::IsFloat
     * @expectedException \InvalidArgumentException
     */
    public function testIsFloatThrowsInvalidArgumentException() {
        Argument::IsFloat(1);
    }

    /** @covers Brickoo\Component\Validation\Argument::IsNotEmpty */
    public function testIsNotEmpty() {
        $this->assertTrue(Argument::IsNotEmpty(true));
        $this->assertTrue(Argument::IsNotEmpty(1));
        $this->assertTrue(Argument::IsNotEmpty("test"));
        $this->assertTrue(Argument::IsNotEmpty(array("test")));
    }

    /**
     * @covers Brickoo\Component\Validation\Argument::IsNotEmpty
     * @expectedException \InvalidArgumentException
     */
    public function testIsNotEmptyThrowsInvalidArgumentException() {
        Argument::IsNotEmpty(false);
    }

    /** @covers Brickoo\Component\Validation\Argument::IsFunctionAvailable */
    public function testIsFunctionAvailable() {
        $this->assertTrue(Argument::IsFunctionAvailable("phpinfo"));
    }

    /**
     * @covers Brickoo\Component\Validation\Argument::IsFunctionAvailable
     * @expectedException \InvalidArgumentException
     */
    public function testIsFunctionAvailableThrowsArgumentException() {
        Argument::IsFunctionAvailable("doesNotExists". time());
    }

    /** @covers Brickoo\Component\Validation\Argument::IsTraversable */
    public function testIsTraversable() {
        $this->assertTrue(Argument::IsTraversable([]));
        $this->assertTrue(Argument::IsTraversable(new Container()));
    }

    /**
     * @covers Brickoo\Component\Validation\Argument::IsTraversable
     * @expectedException \InvalidArgumentException
     */
    public function testIsTraversableThrowsArgumentException() {
        Argument::IsTraversable("wrongType");
    }

    /** @covers Brickoo\Component\Validation\Argument::IsCallable */
    public function testIsCallable() {
        $this->assertTrue(Argument::IsCallable('is_string'));
    }

    /**
     * @covers Brickoo\Component\Validation\Argument::IsCallable
     * @expectedException \InvalidArgumentException
     */
    public function testIsCallableThrowsArgumentException() {
        Argument::IsCallable("iAmNotCallable");
    }

    /** @covers Brickoo\Component\Validation\Argument::IsObject */
    public function testIsObject() {
        $this->assertTrue(Argument::IsObject(new \stdClass()));
    }

    /**
     * @covers Brickoo\Component\Validation\Argument::IsObject
     * @expectedException \InvalidArgumentException
     */
    public function testIsObjectThrowsArgumentException() {
        Argument::IsObject(array("wrongType"));
    }

    /**
     * @covers Brickoo\Component\Validation\Argument::GetInvalidArgumentException
     * @covers Brickoo\Component\Validation\Argument::GetArgumentStringRepresentation
     */
    public function testThrowingAnDefaultInvalidArgumentException() {
        $argument = "test";
        $errorMessage = "Testing throwing an exception";
        $this->setExpectedException("InvalidArgumentException", "Unexpected argument [string] 'test");
        throw Argument::GetInvalidArgumentException($argument, $errorMessage);
    }

    /**
     * @covers Brickoo\Component\Validation\Argument::GetInvalidArgumentException
     * @covers Brickoo\Component\Validation\Argument::GetArgumentStringRepresentation
     */
    public function testThrowingAnObjectInvalidArgumentException() {
        $argument = new \stdClass();
        $errorMessage = "Testing throwing an exception.";

        $this->setExpectedException(
            "InvalidArgumentException",
            sprintf("Unexpected argument [object #%s] stdClass", spl_object_hash($argument))
        );
        throw Argument::GetInvalidArgumentException($argument, $errorMessage);
    }

}
