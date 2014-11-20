<?php

/*
 * Copyright (c) 2011-2014, Celestino Diaz <celestino.diaz@gmx.de>
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

namespace Brickoo\Component\Http\Response;

use Brickoo\Component\Http\HttpMessage;
use Brickoo\Component\Http\HttpResponse;
use Brickoo\Component\Http\HttpStatus;
use Brickoo\Component\Http\HttpResponseBuilder;
use Brickoo\Component\Http\HttpMessageBody;
use Brickoo\Component\Http\HttpMessageHeader;
use Brickoo\Component\Validation\Constraint\ContainsInstancesOfConstraint;

/**
 * SuccessfullyResponse
 *
 * Implements a successfully response.
 * @link http://tools.ietf.org/html/rfc2616#section-10.2.1
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class SuccessfullyResponse extends HttpResponse {

    /**
     * Class constructor.
     * @param string $bodyContent
     * @param array $messageHeaders instances of \Brickoo\Component\Http\HttpHeader
     * @throws \InvalidArgumentException
     */
    public function __construct($bodyContent = "", array $messageHeaders= []) {
        $this->inject(
            (new HttpResponseBuilder())
                ->setHttpStatus(new HttpStatus(HttpStatus::CODE_OK))
                ->setHttpMessage(new HttpMessage(
                    $this->createMessageHeader($messageHeaders),
                    new HttpMessageBody($bodyContent)
                ))
                ->build()
        );
    }

    /**
     * Creates a message header object containing passed headers.
     * @param array $messageHeaders instances of \Brickoo\Component\Http\HttpHeader
     * @throws \InvalidArgumentException
     * @return \Brickoo\Component\Http\HttpMessageHeader
     */
    private function createMessageHeader(array $messageHeaders) {
        if (! (new ContainsInstancesOfConstraint("\\Brickoo\\Component\\Http\\HttpHeader"))->matches($messageHeaders)) {
            throw new \InvalidArgumentException("Invalid message headers.");
        }
        $messageHeader = new HttpMessageHeader();
        foreach ($messageHeaders as $header) {
            $messageHeader->addHeader($header);
        }
        return $messageHeader;
    }

}
