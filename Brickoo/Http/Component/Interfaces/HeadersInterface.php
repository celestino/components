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

    namespace Brickoo\Library\Http\Component\Interfaces;

    use Brickoo\Library\Memory;

    /**
     * HeadersInterface
     *
     * Describes the methods implemented by this interface,
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    interface HeadersInterface extends Memory\Interfaces\ContainerInterface
    {

        /**
        * Returns the accept types supported by the request client.
        * @link http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.1
        * @param string $neededType the type which is needed if supported
        * @return array accept types sorted by priority descening otherwise null
        */
        public function getAcceptTypes();

        /**
         * Checks if the passed type is supported.
         * @param string $acceptType the accept type to check
         * @return boolean check result
         */
        public function isTypeSupported($acceptType);

        /**
         * Returns the accept languages supported by the request client.
         * @link http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.4
         * @return array the languages sorted by priority descening
         */
        public function getAcceptLanguages();

        /**
         * Checks if the passed language is supported.
         * @param string $acceptLanguage the accept language to check
         * @return boolean check result
         */
        public function isLanguageSupported($acceptLanguage);

        /**
         * Returns the accept encodings supported by the request client.
         * @link http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.3
         * @return array the encondings sorted by priority descening
         */
        public function getAcceptEncodings();

        /**
         * Checks if the passed encoding type is supported.
         * @param string $acceptEncoding the accept encoding type to check
         * @return boolean check result
         */
        public function isEncodingSupported($acceptEncoding);

        /**
         * Returns the accept charsets supported by the request client.
         * @link http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.2
         * @return array the charsets sorted by priority descening
         */
        public function getAcceptCharsets();

        /**
         * Checks if the passed encoding type is supported.
         * @param string $acceptEncoding the accept encoding type to check
         * @return boolean check result
         */
        public function isCharsetSupported($acceptCharset);

        /**
         * Imports the request http headers.
         * @return \Brickoo\Library\Http\Component\Headers
         */
        public function importFromGlobals();

        /**
         * Imports the headers from a string.
         * @param string $headers the headers to import
         * @return \Brickoo\Library\Http\Component\Headers
         */
        public function importFromString($headers);

        /**
         * Coverts headers to a request header string.
         * @return string the collected headers
         */
        public function toString();

    }