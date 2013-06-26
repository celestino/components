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

    namespace Brickoo\Http\Builder;

    use Brickoo\Http\Request,
        Brickoo\Http\Request\Header,
        Brickoo\Http\Message\Factory as MessageFactory,
        Brickoo\Http\Request\Factory as RequestFactory,
        Brickoo\Validator\Argument;

    /**
     * RequestBuilder
     *
     * Implements a builder with a fluent interface for the http request class.
     * If a dependency is not set, the corresponding factory is used to create it.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class RequestBuilder implements Interfaces\RequestBuilder {

        /** @var \Brickoo\Http\Message\Interfaces\Header */
        private $Header;

        /** @var \Brickoo\Http\Message\Interfaces\Body */
        private $Body;

        /** @var \Brickoo\Http\Request\Interfaces\Uri */
        private $Uri;

        /** @var string */
        private $method;

        /** @var string */
        private $version;

        /** {@inheritDoc} */
        public function setHeader(\Brickoo\Http\Message\Interfaces\Header $Header) {
            $this->Header = $Header;
            return $this;
        }

        /** {@inheritDoc} */
        public function setBody(\Brickoo\Http\Message\Interfaces\Body $Body) {
            $this->Body = $Body;
            return $this;
        }

        /** {@inheritDoc} */
        public function setUri(\Brickoo\Http\Request\Interfaces\Uri $Uri) {
            $this->Uri = $Uri;
            return $this;
        }

        /** {@inheritDoc} */
        public function setMethod($method) {
            Argument::IsString($method);
            $this->method = $method;
            return $this;
        }

        /** {@inheritDoc} */
        public function setVersion($version) {
            Argument::IsString($version);
            $this->version = $version;
            return $this;
        }

        /** {@inheritDoc} */
        public function build() {
            return new Request($this->getHeader(), $this->getBody(), $this->getUri(), $this->method, $this->version);
        }

        /**
         * Returns the message header dependency.
         * If the dependency is not provided, it will be created using the factory.
         * @return \Brickoo\Http\Message\Interfaces\Header
         */
        private function getHeader() {
            if ($this->Header === null) {
                $this->Header = new Header(MessageFactory\HeaderFactory::Create()->toArray());
            }
            return $this->Header;
        }

        /**
         * Returns the message body dependency.
         * If the dependency is not provided, it will be created using the factory.
         * @return \Brickoo\Http\Message\Interfaces\Body
         */
        private function getBody() {
            if ($this->Body === null) {
                $this->Body = RequestFactory\BodyFactory::Create();
            }
            return $this->Body;
        }

        /**
         * Returns the request url dependency.
         * If the dependency is not provided, it will be created using the factory.
         * @return \Brickoo\Http\Message\Interfaces\Header
         */
        private function getUri() {
            if ($this->Uri === null) {
                $this->Uri = RequestFactory\UriFactory::Create(
                    new RequestFactory\Resolver\UriResolver($this->getHeader()),
                    RequestFactory\QueryFactory::Create()
                );
            }
            return $this->Uri;
        }

    }