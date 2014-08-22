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

namespace Brickoo\Component\Http\Header;

use Brickoo\Component\Validation\Argument;

/**
 * AcceptEncodingHeader
 *
 * Implements an accept-encoding header.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class AcceptEncodingHeader extends GenericHeader {

    use CommonAcceptRoutines;

    /** @var array */
    private $acceptEncodings;

    /**
     * Class constructor.
     * @param string $headerValue
     */
    public function __construct($headerValue = "") {
        parent::__construct("Accept-Encoding", $headerValue);
        $this->acceptEncodings = [];
    }

    /**
     * Sets an accepted encoding with its quality.
     * @param string $acceptEncoding
     * @param float $quality
     * @return \Brickoo\Component\Http\Header\AcceptEncodingHeader
     */
    public function setEncoding($acceptEncoding, $quality = 1.0) {
        Argument::IsString($acceptEncoding);
        Argument::IsFloat($quality);

        $this->getEncodings();
        $this->acceptEncodings[$acceptEncoding] = $quality;
        $this->headerValue = $this->buildValue($this->acceptEncodings);
        return $this;
    }

    /**
     * Returns the accepted encodings.
     * @return array the accepted encodings
     */
    public function getEncodings() {
        if (empty($this->acceptEncodings)) {
            $this->acceptEncodings = $this->getHeaderValues($this->getValue());
        }
        return $this->acceptEncodings;
    }

    /**
     * Checks if the passed encoding type is supported.
     * @param string $encoding the encoding type to check
     * @return boolean check result
     */
    public function isEncodingSupported($encoding) {
        Argument::IsString($encoding);
        return array_key_exists($encoding, $this->getEncodings());
    }

}
