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

use Brickoo\Http\Authority,
    Brickoo\Http\Query,
    Brickoo\Http\Uri,
    Brickoo\Http\Resolver\UriResolver;

/**
 * UriFactory
 *
 * Implements a http uri factory.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class UriFactory {

    /**
     * Creates a request uri instance.
     * @param \Brickoo\Http\Resolver\UriResolver $uriResolver
     * @return \Brickoo\Http\Uri
     */
    public function create(UriResolver $uriResolver) {
        return new Uri(
            $uriResolver->getScheme(),
            $this->createAuthority($uriResolver),
            $uriResolver->getPath(),
            $this->createQuery($uriResolver),
            $uriResolver->getFragment()
       );
    }

    /**
     * Creates the authority dependency.
     * @param \Brickoo\Http\Resolver\UriResolver $uriResolver
     * @return \Brickoo\Http\Authority
     */
    private function createAuthority(UriResolver $uriResolver) {
        return new Authority($uriResolver->getHostname(), $uriResolver->getPort());
    }

    /**
     * Creates the query dependency.
     * @param \Brickoo\Http\Resolver\UriResolver $uriResolver
     * @return \Brickoo\Http\Query
     */
    private function createQuery(UriResolver $uriResolver) {
        return (new Query())->fromString($uriResolver->getQueryString());
    }

}