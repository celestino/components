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

namespace Brickoo\Http;

use Brickoo\Validation\Argument;

/**
 * MessageBody
 *
 * Implements a http message body.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class MessageBody {

    /** @var string */
    protected $content;

    /**
     * Class constructor.
     * @param string $content the body content
     * @throws \InvalidArgumentException
     * @return void
     */
    public function __construct($content = "") {
        Argument::IsString($content);
        $this->content = $content;
    }

    /**
     * Returns the message body.
     * @return string the body
     */
    public function getContent() {
        return $this->content;
    }

    /**
     * Sets the content of the message body.
     * @param string $content the body content to set
     * @throws \InvalidArgumentException
     * @return \Brickoo\Http\MessageBody
     */
    public function setContent($content) {
        Argument::IsString($content);
        $this->content = $content;
        return $this;
    }

    /**
     * Imports the content from input source.
     * @return \Brickoo\Http\Message\MessageBody
     */
    public function importFromInput() {
        $this->content = file_get_contents("php://input");
        return $this;
    }

}