<?php

/*
 * Copyright (c) 2011-2015, Celestino Diaz <celestino.diaz@gmx.de>
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

namespace Brickoo\Tests\Component\Validation;

use Brickoo\Component\Validation\Argument;
use Brickoo\Component\Common\Container;
use PHPUnit_Framework_Error_Warning;
use PHPUnit_Framework_TestCase;

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
