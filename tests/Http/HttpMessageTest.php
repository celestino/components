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

use Brickoo\Component\Http\HttpMessage;
use PHPUnit_Framework_TestCase;

/**
 * HttpMessage
 *
 * Test suite for the HttpMessage class.
 * @see Brickoo\Component\Http\HttpMessage
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class HttpMessageTest extends PHPUnit_Framework_TestCase {

    /**
     * @covers Brickoo\Component\Http\HttpMessage::__construct
     * @covers Brickoo\Component\Http\HttpMessage::getHeader
     */
    public function testGetHeader() {
        $messageHeader = $this->getMessageHeaderStub();
        $httpMessage = new HttpMessage($messageHeader, $this->getHttpMessageBodyStub());
        $this->assertSame($messageHeader, $httpMessage->getHeader());
    }

    /**
     * @covers Brickoo\Component\Http\HttpMessage::__construct
     * @covers Brickoo\Component\Http\HttpMessage::getBody
     */
    public function testGetBody() {
        $messageBody = $this->getHttpMessageBodyStub();
        $httpMessage = new HttpMessage($this->getMessageHeaderStub(), $messageBody);
        $this->assertSame($messageBody, $httpMessage->getBody());
    }

    /**
     * Returns a message header stub.
     * @return \Brickoo\Component\Http\HttpMessageHeader
     */
    private function getMessageHeaderStub() {
        return $this->getMockBuilder("\\Brickoo\\Component\\Http\\HttpMessageHeader")
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * Returns a message body stub.
     * @return \Brickoo\Component\Http\HttpMessageBody
     */
    private function getHttpMessageBodyStub() {
        return $this->getMockBuilder("\\Brickoo\\Component\\Http\\HttpMessageBody")
            ->disableOriginalConstructor()
            ->getMock();
    }

}
