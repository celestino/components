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

namespace Brickoo\Component\Http\Response;

use Brickoo\Component\Http\HttpResponse;
use Brickoo\Component\Http\HttpStatus;
use Brickoo\Component\Http\HttpResponseBuilder;

/**
 * InternalServerErrorResponse
 *
 * Implements an internal server error response.
 * @link http://tools.ietf.org/html/rfc2616#section-10.5.1
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class InternalServerErrorResponse extends HttpResponse {

    public function __construct() {
        $this->inject(
            (new HttpResponseBuilder())
                ->setHttpStatus(new HttpStatus(HttpStatus::CODE_INTERNAL_SERVER_ERROR))
                ->build()
        );
    }

}
