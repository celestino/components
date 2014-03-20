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
 * AcceptCharsetHeader
 *
 * Implements an accept-charset header.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class AcceptCharsetHeader extends GenericHeader {

    use CommonAcceptRoutines;

    /** @var array */
    private $acceptCharsets;

    /**
     * Class constructor.
     * @param string $headerValue
     */
    public function __construct($headerValue = "") {
        parent::__construct("Accept-Charset", $headerValue);
        $this->acceptCharsets = [];
    }

    /**
     * Sets an accepted charset with its quality.
     * @param string $acceptCharset
     * @param float $quality
     * @return \Brickoo\Component\Http\Header\AcceptCharsetHeader
     */
    public function setCharset($acceptCharset, $quality = 1.0) {
        Argument::IsString($acceptCharset);
        Argument::IsFloat($quality);

        $this->getCharsets();
        $this->acceptCharsets[$acceptCharset] = $quality;
        $this->headerValue = $this->buildValue($this->acceptCharsets);
        return $this;
    }
    /**
     * Returns the accepted charsets.
     * @return array the accepted charsets
     */
    public function getCharsets() {
        if (empty($this->acceptCharsets)) {
            $this->acceptCharsets = $this->getHeaderValues($this->getValue());
        }
        return $this->acceptCharsets;
    }

    /**
     * Checks if the passed encoding type is supported.
     * @param string $charset the charset to check
     * @return boolean check result
     */
    public function isCharsetSupported($charset) {
        Argument::IsString($charset);
        return array_key_exists($charset, $this->getCharsets());
    }

}