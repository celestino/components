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

namespace Brickoo\Component\Http\Response;

use Brickoo\Component\Http\HttpMessage,
    Brickoo\Component\Http\HttpResponse,
    Brickoo\Component\Http\HttpStatus,
    Brickoo\Component\Http\HttpVersion,
    Brickoo\Component\Http\HttpMessageBody,
    Brickoo\Component\Http\HttpMessageHeader,
    Brickoo\Component\Validation\Constraint\ContainsInstancesOfConstraint;

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
        parent::__construct(
            new HttpVersion(HttpVersion::HTTP_1_1),
            new HttpStatus(HttpStatus::CODE_OK),
            new HttpMessage(
                $this->createMessageHeader($messageHeaders),
                new HttpMessageBody($bodyContent)
            )
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
