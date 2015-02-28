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

namespace Brickoo\Tests\Component\Http;

use Brickoo\Component\Http\Header\AuthorizationHeaderField;
use PHPUnit_Framework_TestCase;

/**
 * AuthorizationHeaderFieldTest
 *
 * Test suite for the AuthorizationHeaderField class.
 * @see Brickoo\Component\Http\Header\AuthorizationHeaderField
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class AuthorizationHeaderFieldTest extends PHPUnit_Framework_TestCase {

    /**
     * @covers Brickoo\Component\Http\Header\AuthorizationHeaderField::__construct
     * @covers Brickoo\Component\Http\Header\AuthorizationHeaderField::toString
     */
    public function testToString() {
        $headerFieldValue = "Basic ". base64_encode("user:pwd");
        $authorizationHeader = new AuthorizationHeaderField($headerFieldValue);
        $this->assertEquals("Authorization: ".$headerFieldValue, $authorizationHeader->toString());
    }

}
