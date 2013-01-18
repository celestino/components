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
     * 3. Neither the name of Brickoo nor the names of its contributors may be used
     *    to endorse or promote products derived from this software without specific
     *    prior written permission.
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

    namespace Brickoo\Http\Request\Factory;

    use Brickoo\Validator\Argument;

    /**
     * UriFactory
     *
     * Describes a factory for a http request uri.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class UriFactory {

        /**
         * Creates an requets uri object using the factory uri resolver.
         * @param \Brickoo\Http\Request\Factory\Resolver\UriResolver $UriResolver
         * @param \Brickoo\Http\Request\Interfaces\Query $Query
         * @return \Brickoo\Http\Request\Uri
         */
        public static function Create(
            \Brickoo\Http\Request\Factory\Resolver\UriResolver $UriResolver,
            \Brickoo\Http\Request\Interfaces\Query $Query
        ){
            return new \Brickoo\Http\Request\Uri(
                $UriResolver->getScheme(),
                $UriResolver->getHostname(),
                $UriResolver->getPort(),
                $UriResolver->getPath(),
                $Query,
                $UriResolver->getPathInfo()
            );
        }

        /**
         * Create a request uri object using the extracted uri values.
         * @param string $uri the uri to extract the values from
         * @throws \InvalidArgumentException if the argument is not valid
         * @return \Brickoo\Http\Request\Uri
         */
        public static function CreateFromString($uri) {
            Argument::IsString($uri);

            if (! preg_match("~^[^@:/?#]+:(.*@)?//[^/?#]+(\?[^#]*)?(#.*)?~", $uri)) {
                throw new \InvalidArgumentException(sprintf("The argument `%s` does not match a valid uri.", $uri));
            }

            $defaultValues = array(
                "scheme" => "http",
                "host" => "localhost",
                "path" => "/",
                "query" => ""
            );

            $uriParts = array_merge($defaultValues, parse_url($uri));

            if (! isset($uriParts["port"])) {
                $uriParts["port"] = $uriParts["scheme"] == "https" ? 443 : 80;
            }

            return new \Brickoo\Http\Request\Uri(
                $uriParts["scheme"],
                $uriParts["host"],
                $uriParts["port"],
                $uriParts["path"],
                QueryFactory::CreateFromString($uriParts["query"])
            );
        }

    }