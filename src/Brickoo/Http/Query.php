<?php

/*
 * Copyright (c) 2011-2013, Celestino Diaz <celestino.diaz@gmx.de>.
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

use Brickoo\Memory\Container,
    Brickoo\Validation\Argument;

/**
 * Query
 *
 * Implements a http query parameters container.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class Query extends Container {

    /**
     * Converts the query parameters to a request query string.
     * The query string is encoded as of the RFC3986.
     * @return string the query string
     */
    public function toString() {
        return str_replace("+", "%20", http_build_query($this->toArray()));
    }

    /**
     * Imports the query parameters from the extracted key/value pairs.
     * @param strin $query the query to extract the pairs from
     * @throws \InvalidArgumentException if the argument is not valid
     * @return \Brickoo\Http\Query
     */
    public function fromString($query) {
        Argument::IsString($query);

        if (($position = strpos($query, "?")) !== false) {
            $query = substr($query, $position + 1);
        }

        parse_str(rawurldecode($query), $importedQueryParameters);
        $this->fromArray($importedQueryParameters);
        return $this;
    }

}