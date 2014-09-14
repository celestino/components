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
    PHPUnit_Framework_Error_Warning,
    PHPUnit_Framework_TestCase;

/**
 * ArgumentTest
 *
 * Test suite for the Argument class.
 * @see Brickoo\Component\Validation\Argument
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class ArgumentTest extends PHPUnit_Framework_TestCase {

    /** {@inheritdoc} */
    public function tearDown() {
        Argument::$throwExceptions = true;
    }

    /** @covers Brickoo\Component\Validation\Argument::isString */
    public function testIsString() {
        $this->assertTrue(Argument::isString("test"));
    }

    /**
     * @covers Brickoo\Component\Validation\Argument::isString
     * @covers Brickoo\Component\Validation\Argument::handleArgumentValidation
     * @expectedException \InvalidArgumentException
     */
    public function testIsStringThrowsInvalidArgumentException() {
        Argument::isString(new \stdClass());
    }

    /** @covers Brickoo\Component\Validation\Argument::isInteger */
    public function testIsInteger() {
        $this->assertTrue(Argument::isInteger(1234));
        $this->assertTrue(Argument::isInteger(0));
    }

    /**
     * @covers Brickoo\Component\Validation\Argument::isInteger
     * @expectedException \InvalidArgumentException
     */
    public function testIsIntegerThrowsInvalidArgumentException() {
        Argument::isInteger(new \stdClass());
    }

    /** @covers Brickoo\Component\Validation\Argument::isStringOrInteger */
    public function testIsStringOrInteger() {
        $this->assertTrue(Argument::isStringOrInteger(''));
        $this->assertTrue(Argument::isStringOrInteger(0));
    }

    /**
     * @covers Brickoo\Component\Validation\Argument::isStringOrInteger
     * @expectedException \InvalidArgumentException
     */
    public function testIsStringOrIntegerThrowsInvalidArgumentException() {
        Argument::isStringOrInteger(array("wrongType"));
    }

    /** @covers Brickoo\Component\Validation\Argument::isBoolean */
    public function testIsBoolean() {
        $this->assertTrue(Argument::isBoolean(true));
        $this->assertTrue(Argument::isBoolean(false));
    }

    /**
     * @covers Brickoo\Component\Validation\Argument::isBoolean
     * @expectedException \InvalidArgumentException
     */
    public function testIsBooleanThrowsInvalidArgumentException() {
        Argument::isBoolean(null);
    }

    /** @covers Brickoo\Component\Validation\Argument::isFloat */
    public function testIsFloat() {
        $this->assertTrue(Argument::isFloat(1.234));
    }

    /**
     * @covers Brickoo\Component\Validation\Argument::isFloat
     * @expectedException \InvalidArgumentException
     */
    public function testIsFloatThrowsInvalidArgumentException() {
        Argument::isFloat(1);
    }

    /** @covers Brickoo\Component\Validation\Argument::isNotEmpty */
    public function testIsNotEmpty() {
        $this->assertTrue(Argument::isNotEmpty(true));
        $this->assertTrue(Argument::isNotEmpty(1));
        $this->assertTrue(Argument::isNotEmpty("test"));
        $this->assertTrue(Argument::isNotEmpty(array("test")));
    }

    /**
     * @covers Brickoo\Component\Validation\Argument::isNotEmpty
     * @expectedException \InvalidArgumentException
     */
    public function testIsNotEmptyThrowsInvalidArgumentException() {
        Argument::isNotEmpty(false);
    }

    /** @covers Brickoo\Component\Validation\Argument::isFunctionAvailable */
    public function testIsFunctionAvailable() {
        $this->assertTrue(Argument::isFunctionAvailable("phpinfo"));
    }

    /**
     * @covers Brickoo\Component\Validation\Argument::isFunctionAvailable
     * @expectedException \InvalidArgumentException
     */
    public function testIsFunctionAvailableThrowsArgumentException() {
        Argument::isFunctionAvailable("doesNotExists".time());
    }

    /** @covers Brickoo\Component\Validation\Argument::isTraversable */
    public function testIsTraversable() {
        $this->assertTrue(Argument::isTraversable([]));
        $this->assertTrue(Argument::isTraversable(new Container()));
    }

    /**
     * @covers Brickoo\Component\Validation\Argument::isTraversable
     * @expectedException \InvalidArgumentException
     */
    public function testIsTraversableThrowsArgumentException() {
        Argument::isTraversable("wrongType");
    }

    /** @covers Brickoo\Component\Validation\Argument::isCallable */
    public function testIsCallable() {
        $this->assertTrue(Argument::isCallable('is_string'));
    }

    /**
     * @covers Brickoo\Component\Validation\Argument::isCallable
     * @expectedException \InvalidArgumentException
     */
    public function testIsCallableThrowsArgumentException() {
        Argument::isCallable("iAmNotCallable");
    }

    /** @covers Brickoo\Component\Validation\Argument::isObject */
    public function testIsObject() {
        $this->assertTrue(Argument::isObject(new \stdClass()));
    }

    /**
     * @covers Brickoo\Component\Validation\Argument::isObject
     * @expectedException \InvalidArgumentException
     */
    public function testIsObjectThrowsArgumentException() {
        Argument::isObject(array("wrongType"));
    }

    /** @covers Brickoo\Component\Validation\Argument::isResource */
    public function testIsResource() {
        $file = fopen("php://memory", "r");
        $this->assertTrue(Argument::isResource($file));
        fclose($file);
    }

    /**
     * @covers Brickoo\Component\Validation\Argument::isResource
     * @expectedException \InvalidArgumentException
     */
    public function testIsResourceThrowsArgumentException() {
        Argument::isResource("wrongType");
    }

    /**
     * @covers Brickoo\Component\Validation\Argument::getInvalidArgumentException
     * @covers Brickoo\Component\Validation\Argument::getArgumentStringRepresentation
     */
    public function testThrowingAnDefaultInvalidArgumentException() {
        $argument = "test";
        $errorMessage = "Testing throwing an exception";
        $this->setExpectedException("InvalidArgumentException", "Unexpected argument [string] 'test");
        throw Argument::GetInvalidArgumentException($argument, $errorMessage);
    }

    /**
     * @covers Brickoo\Component\Validation\Argument::getInvalidArgumentException
     * @covers Brickoo\Component\Validation\Argument::getErrorMessage
     * @covers Brickoo\Component\Validation\Argument::getArgumentStringRepresentation
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

    /**
     * @covers Brickoo\Component\Validation\Argument::handleArgumentValidation
     * @covers Brickoo\Component\Validation\Argument::getErrorMessage
     * @covers Brickoo\Component\Validation\Argument::getArgumentStringRepresentation
     * @expectedException PHPUnit_Framework_Error_Warning
     */
    public function testTriggeringError() {
        Argument::$throwExceptions = false;
        $argument = new \stdClass();
        $errorMessage = "Testing triggering an error.";

        $this->assertFalse(Argument::handleArgumentValidation(false, $argument, $errorMessage));
    }

    /**
     * @covers Brickoo\Component\Validation\Argument::handleArgumentValidation
     * @covers Brickoo\Component\Validation\Argument::getErrorMessage
     * @covers Brickoo\Component\Validation\Argument::getArgumentStringRepresentation
     */
    public function testTriggeringErrorReturnFalse() {
        Argument::$throwExceptions = false;
        $argument = new \stdClass();
        $errorMessage = "Testing triggering an error.";

        $this->assertFalse(@Argument::handleArgumentValidation(false, $argument, $errorMessage));
    }

}
