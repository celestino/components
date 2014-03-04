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

use Brickoo\Component\Http\Header\GenericHeader,
    Brickoo\Component\Validation\Argument;

/**
 * AcceptHeader
 *
 * Implements an accept header.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class AcceptHeader extends GenericHeader {

    use \Brickoo\Component\Http\Header\CommonAcceptRoutines;

    /** @var array */
    private $acceptTypes;

    /**
     * Class constructor.
     * @param string $headerValue
     * @return void
     */
    public function __construct($headerValue = "") {
        parent::__construct("Accept", $headerValue);
        $this->acceptTypes = [];
    }

    /**
     * Sets an accept type with its quality.
     * @param string $acceptType
     * @param float $quality
     * @return \Brickoo\Component\Http\Header\AcceptHeader
     */
    public function setType($acceptType, $quality = 1.0) {
        Argument::IsString($acceptType);
        Argument::IsFloat($quality);

        $this->getTypes();
        $this->acceptTypes[$acceptType] = $quality;
        $this->headerValue = $this->buildValue($this->acceptTypes);
        return $this;
    }

    /**
     * Returns the accepted types.
     * @return array the accepted types
     */
    public function getTypes() {
        if (empty($this->acceptTypes)) {
            $this->acceptTypes = $this->getHeaderValues($this->getValue());
        }
        return $this->acceptTypes;
    }

    /**
     * Checks if the passed type is supported.
     * @param string $type the type to check
     * @return boolean check result
     */
    public function isTypeSupported($type) {
        Argument::IsString($type);
        return array_key_exists($type, $this->getTypes());
    }

}