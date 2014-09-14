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

namespace Brickoo\Tests\Component\Http;

use Brickoo\Component\Http\HttpMessage,
    PHPUnit_Framework_TestCase;

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
