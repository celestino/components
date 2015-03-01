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

namespace Brickoo\Tests\Component\Common;

use Brickoo\Component\Common\Assert;
use Brickoo\Component\Common\Container;
use PHPUnit_Framework_Error_Warning;
use PHPUnit_Framework_TestCase;

/**
 * ArgumentTest
 *
 * Test suite for the Assert class.
 * @see Brickoo\Component\Common\Assert
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class ArgumentTest extends PHPUnit_Framework_TestCase {

    /** {@inheritdoc} */
    public function tearDown() {
        Assert::$throwExceptions = true;
    }

    /**
     * @covers Brickoo\Component\Common\Assert::isString
     * @covers Brickoo\Component\Common\Assert::handleArgumentValidation
     */
    public function testIsString() {
        $this->assertTrue(Assert::isString("test"));
    }

    /**
     * @covers Brickoo\Component\Common\Assert::isString
     * @covers Brickoo\Component\Common\Assert::handleArgumentValidation
     * @expectedException \InvalidArgumentException
     */
    public function testIsStringThrowsInvalidArgumentException() {
        Assert::isString(new \stdClass());
    }

    /**
     * @covers Brickoo\Component\Common\Assert::isInteger
     * @covers Brickoo\Component\Common\Assert::handleArgumentValidation
     */
    public function testIsInteger() {
        $this->assertTrue(Assert::isInteger(1234));
        $this->assertTrue(Assert::isInteger(0));
    }

    /**
     * @covers Brickoo\Component\Common\Assert::isInteger
     * @expectedException \InvalidArgumentException
     */
    public function testIsIntegerThrowsInvalidArgumentException() {
        Assert::isInteger(new \stdClass());
    }

    /**
     * @covers Brickoo\Component\Common\Assert::isStringOrInteger
     * @covers Brickoo\Component\Common\Assert::handleArgumentValidation
     */
    public function testIsStringOrInteger() {
        $this->assertTrue(Assert::isStringOrInteger(''));
        $this->assertTrue(Assert::isStringOrInteger(0));
    }

    /**
     * @covers Brickoo\Component\Common\Assert::isStringOrInteger
     * @expectedException \InvalidArgumentException
     */
    public function testIsStringOrIntegerThrowsInvalidArgumentException() {
        Assert::isStringOrInteger(array("wrongType"));
    }

    /**
     * @covers Brickoo\Component\Common\Assert::isBoolean
     * @covers Brickoo\Component\Common\Assert::handleArgumentValidation
     */
    public function testIsBoolean() {
        $this->assertTrue(Assert::isBoolean(true));
        $this->assertTrue(Assert::isBoolean(false));
    }

    /**
     * @covers Brickoo\Component\Common\Assert::isBoolean
     * @expectedException \InvalidArgumentException
     */
    public function testIsBooleanThrowsInvalidArgumentException() {
        Assert::isBoolean(null);
    }

    /**
     * @covers Brickoo\Component\Common\Assert::isFloat
     * @covers Brickoo\Component\Common\Assert::handleArgumentValidation
     */
    public function testIsFloat() {
        $this->assertTrue(Assert::isFloat(1.234));
    }

    /**
     * @covers Brickoo\Component\Common\Assert::isFloat
     * @expectedException \InvalidArgumentException
     */
    public function testIsFloatThrowsInvalidArgumentException() {
        Assert::isFloat(1);
    }

    /**
     * @covers Brickoo\Component\Common\Assert::isNotEmpty
     * @covers Brickoo\Component\Common\Assert::handleArgumentValidation
     */
    public function testIsNotEmpty() {
        $this->assertTrue(Assert::isNotEmpty(true));
        $this->assertTrue(Assert::isNotEmpty(1));
        $this->assertTrue(Assert::isNotEmpty("test"));
        $this->assertTrue(Assert::isNotEmpty(array("test")));
    }

    /**
     * @covers Brickoo\Component\Common\Assert::isNotEmpty
     * @expectedException \InvalidArgumentException
     */
    public function testIsNotEmptyThrowsInvalidArgumentException() {
        Assert::isNotEmpty(false);
    }

    /**
     * @covers Brickoo\Component\Common\Assert::isFunctionAvailable
     * @covers Brickoo\Component\Common\Assert::handleArgumentValidation
     */
    public function testIsFunctionAvailable() {
        $this->assertTrue(Assert::isFunctionAvailable("phpinfo"));
    }

    /**
     * @covers Brickoo\Component\Common\Assert::isFunctionAvailable
     * @expectedException \InvalidArgumentException
     */
    public function testIsFunctionAvailableThrowsArgumentException() {
        Assert::isFunctionAvailable("doesNotExists".time());
    }

    /**
     * @covers Brickoo\Component\Common\Assert::isTraversable
     * @covers Brickoo\Component\Common\Assert::handleArgumentValidation
     */
    public function testIsTraversable() {
        $this->assertTrue(Assert::isTraversable([]));
        $this->assertTrue(Assert::isTraversable(new Container()));
    }

    /**
     * @covers Brickoo\Component\Common\Assert::isTraversable
     * @expectedException \InvalidArgumentException
     */
    public function testIsTraversableThrowsArgumentException() {
        Assert::isTraversable("wrongType");
    }

    /**
     * @covers Brickoo\Component\Common\Assert::isCallable
     * @covers Brickoo\Component\Common\Assert::handleArgumentValidation
     */
    public function testIsCallable() {
        $this->assertTrue(Assert::isCallable('is_string'));
    }

    /**
     * @covers Brickoo\Component\Common\Assert::isCallable
     * @expectedException \InvalidArgumentException
     */
    public function testIsCallableThrowsArgumentException() {
        Assert::isCallable("iAmNotCallable");
    }

    /**
     * @covers Brickoo\Component\Common\Assert::isObject
     * @covers Brickoo\Component\Common\Assert::handleArgumentValidation
     */
    public function testIsObject() {
        $this->assertTrue(Assert::isObject(new \stdClass()));
    }

    /**
     * @covers Brickoo\Component\Common\Assert::isObject
     * @expectedException \InvalidArgumentException
     */
    public function testIsObjectThrowsArgumentException() {
        Assert::isObject(array("wrongType"));
    }

    /**
     * @covers Brickoo\Component\Common\Assert::isResource
     * @covers Brickoo\Component\Common\Assert::handleArgumentValidation
     */
    public function testIsResource() {
        $file = fopen("php://memory", "r");
        $this->assertTrue(Assert::isResource($file));
        fclose($file);
    }

    /**
     * @covers Brickoo\Component\Common\Assert::isResource
     * @expectedException \InvalidArgumentException
     */
    public function testIsResourceThrowsArgumentException() {
        Assert::isResource("wrongType");
    }

    /**
     * @covers Brickoo\Component\Common\Assert::getInvalidArgumentException
     * @covers Brickoo\Component\Common\Assert::getArgumentStringRepresentation
     */
    public function testThrowingAnDefaultInvalidArgumentException() {
        $argument = "test";
        $errorMessage = "Testing throwing an exception";
        $this->setExpectedException("InvalidArgumentException", "Unexpected argument [string] 'test");
        throw Assert::GetInvalidArgumentException($argument, $errorMessage);
    }

    /**
     * @covers Brickoo\Component\Common\Assert::getInvalidArgumentException
     * @covers Brickoo\Component\Common\Assert::getErrorMessage
     * @covers Brickoo\Component\Common\Assert::getArgumentStringRepresentation
     */
    public function testThrowingAnObjectInvalidArgumentException() {
        $argument = new \stdClass();
        $errorMessage = "Testing throwing an exception.";

        $this->setExpectedException(
            "InvalidArgumentException",
            sprintf("Unexpected argument [object #%s] stdClass", spl_object_hash($argument))
        );
        throw Assert::GetInvalidArgumentException($argument, $errorMessage);
    }

    /**
     * @covers Brickoo\Component\Common\Assert::handleArgumentValidation
     * @covers Brickoo\Component\Common\Assert::getErrorMessage
     * @covers Brickoo\Component\Common\Assert::getArgumentStringRepresentation
     * @expectedException PHPUnit_Framework_Error_Warning
     */
    public function testTriggeringError() {
        Assert::$throwExceptions = false;
        $argument = new \stdClass();
        $errorMessage = "Testing triggering an error.";

        $this->assertFalse(Assert::handleArgumentValidation(false, $argument, $errorMessage));
    }

    /**
     * @covers Brickoo\Component\Common\Assert::handleArgumentValidation
     * @covers Brickoo\Component\Common\Assert::getErrorMessage
     * @covers Brickoo\Component\Common\Assert::getArgumentStringRepresentation
     */
    public function testTriggeringErrorReturnFalse() {
        Assert::$throwExceptions = false;
        $argument = new \stdClass();
        $errorMessage = "Testing triggering an error.";

        $this->assertFalse(@Assert::handleArgumentValidation(false, $argument, $errorMessage));
    }

}
