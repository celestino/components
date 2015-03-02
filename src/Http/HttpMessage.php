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

namespace Brickoo\Component\Http;

/**
 * HttpMessage
 *
 * Implements a http message.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class HttpMessage {

    /** @var \Brickoo\Component\Http\HttpMessageHeader */
    private $messageHeader;

    /** @var \Brickoo\Component\Http\HttpMessageBody */
    private $messageBody;

    /**
     * Class constructor.
     * @param \Brickoo\Component\Http\HttpMessageHeader $messageHeader
     * @param \Brickoo\Component\Http\HttpMessageBody $messageBody
     */
    public function __construct(HttpMessageHeader $messageHeader, HttpMessageBody $messageBody) {
        $this->messageHeader = $messageHeader;
        $this->messageBody = $messageBody;
    }

    /**
     * Returns the message header.
     * @return \Brickoo\Component\Http\HttpMessageHeader
     */
    public function getHeader() {
        return $this->messageHeader;
    }

    /**
     * Returns the message body.
     * @return \Brickoo\Component\Http\HttpMessageBody
     */
    public function getBody() {
        return $this->messageBody;
    }

}
