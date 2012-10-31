<?php

    /*
     * Copyright (c) 2011-2012, Celestino Diaz <celestino.diaz@gmx.de>.
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
     * Url
     *
     * Describes a factory for a http request url.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class Url {

        /**
         * Creates an requets url object using the factory url resolver.
         * @param \Brickoo\Http\Request\Factory\Resolver\Url $UrlResolver
         * @param \Brickoo\Http\Request\Interfaces\Query $Query
         * @return \Brickoo\Http\Request\Url
         */
        public static function Create(
            \Brickoo\Http\Request\Factory\Resolver\Url $UrlResolver,
            \Brickoo\Http\Request\Interfaces\Query $Query
        ){
            return new \Brickoo\Http\Request\Url(
                $UrlResolver->getScheme(),
                $UrlResolver->getHostname(),
                $UrlResolver->getPort(),
                $UrlResolver->getPath(),
                $Query
            );
        }

        /**
         * Create a request url object using the extracted url values.
         * @param string $url the url to extract the values from
         * @throws \InvalidArgumentException if the argument is not valid
         * @return \Brickoo\Http\Request\Url
         */
        public static function CreateFromString($url) {
            Argument::IsString($url);

            if (! preg_match("~^[^@:/?#]+:(.*@)?//[^/?#]+(\?[^#]*)?(#.*)?~", $url)) {
                throw new \InvalidArgumentException(sprintf("The url `%s` does not match a valid URL", $url));
            }

            preg_match("~^(?<scheme>[^@:/?#]+)://(.*@)?(?<hostname>[^/?:#]*)(:(?<port>\d+))?(?<path>[^?#]*)(\?(?<query>[^#]*))?~u", $url, $urlParts);

            settype($urlParts["port"], "integer");

            if ($urlParts["port"] == 0) {
                $urlParts["port"] = $urlParts["scheme"] == "https" ? 443 : 80;
            }

            return new \Brickoo\Http\Request\Url(
                $urlParts["scheme"],
                $urlParts["hostname"],
                $urlParts["port"],
                $urlParts["path"],
                Query::CreateFromString($urlParts["query"])
            );
        }

    }