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
    Brickoo\Component\Http\MessageBody,
    Brickoo\Component\Http\MessageHeader,
    Brickoo\Component\Http\Header\GenericHeader;

/**
 * TemporaryRedirectResponse
 *
 * Implements a temporary redirect response.
 * Request method remain the same.
 * @link http://tools.ietf.org/html/rfc2616#section-10.3.8
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class TemporaryRedirectResponse extends HttpResponse {

    /**
     * Class constructor.
     * @param String $location the redirect location
     */
    public function __construct($location) {
        parent::__construct(
            new HttpVersion(HttpVersion::HTTP_1_1),
            new HttpStatus(HttpStatus::CODE_TEMPORARY_REDIRECT),
            new HttpMessage(
                (new MessageHeader())->setHeader(new GenericHeader("Location", $location)),
                new MessageBody()
            )
        );
    }

}
