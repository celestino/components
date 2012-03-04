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

    namespace Brickoo\Http;

    use Brickoo\Template,
        Brickoo\Validator\TypeValidator;

    /**
     * Response
     *
     * Implements methods to create a HTTP qualified response.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class Response implements Interfaces\ResponseInterface
    {

        /**
         * Holds the corresponding status code phrases.
         * 1xx: Informational - Request received, continuing process
         * 2xx: Success - The action was successfully received, understood, and accepted
         * 3xx: Redirection - Further action must be taken in order to complete the request
         * 4xx: Client Error - The request contains bad syntax or cannot be fulfilled
         * 5xx: Server Error - The server failed to fulfill an apparently valid request
         * @link http://tools.ietf.org/html/rfc2616#page-40
         * @var array
         */
        protected $statusPhrases = array(
            100 => 'Continue',
            101 => 'Switching Protocols',
            200 => 'OK',
            201 => 'Created',
            202 => 'Accepted',
            203 => 'Non-Authoritative Information',
            204 => 'No Content',
            205 => 'Reset Content',
            206 => 'Partial Content',
            300 => 'Multiple Choices',
            301 => 'Moved Permanently',
            302 => 'Found',
            303 => 'See Other',
            304 => 'Not Modified',
            305 => 'Use Proxy',
            307 => 'Temporary Redirect',
            400 => 'Bad Request',
            401 => 'Unauthorized',
            402 => 'Payment Required',
            403 => 'Forbidden',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            406 => 'Not Acceptable',
            407 => 'Proxy Authentication Required',
            408 => 'Request Time-out',
            409 => 'Conflict',
            410 => 'Gone',
            411 => 'Length Required',
            412 => 'Precondition Failed',
            413 => 'Request Entity Too Large',
            414 => 'Request-URI Too Large',
            415 => 'Unsupported Media Type',
            416 => 'Requested range not satisfiable',
            417 => 'Expectation Failed',
            500 => 'Internal Server Error',
            501 => 'Not Implemented',
            502 => 'Bad Gateway',
            503 => 'Service Unavailable',
            504 => 'Gateway Time-out',
            505 => 'HTTP Version not supported'
        );

        /**
         * Holds the class dependencies.
         * @var array
         */
        protected $dependencies;

        /**
         * Returns the dependency holded, created or overwritten.
         * @param string $name the name of the dependency
         * @param string $interface the interface which has to be implemented by the dependency
         * @param callback $callback the callback to create a new dependency
         * @param object $Dependency the dependecy to inject
         * @return object Request if overwritten otherwise the dependency
         */
        protected function getDependency($name, $interface, $callback, $Dependency = null)
        {
            if ($Dependency instanceof $interface) {
                $this->dependencies[$name] = $Dependency;
                return $this;
            }
            elseif ((! isset($this->dependencies[$name])) || (! $this->dependencies[$name] instanceof $interface)) {
                $this->dependencies[$name] = call_user_func($callback, $this);
            }
            return $this->dependencies[$name];
        }

        /**
         * Injects a response template dependency.
         * @param \Brickoo\Template\Interfaces\TemplateInterface $Template the Template to inject
         * @throws Exceptions\ResponseTemplateNotAvailableException if trying to retrieve the not injected dependency
         * @return \Brickoo\Template\Interfaces\TemplateInterface
         */
        public function Template(\Brickoo\Template\Interfaces\TemplateInterface $Template = null)
        {
            return $this->getDependency(
                'Template',
                '\Brickoo\Template\Interfaces\TemplateInterface',
                function() {throw new Exceptions\ResponseTemplateNotAvailableException();},
                $Template
            );
        }

        /**
         * Checks if the Response has a template dependency injected.
         * @return boolean check result
         */
        public function hasTemplate()
        {
            return (
                (isset($this->dependencies['Template'])) &&
                ($this->dependencies['Template'] instanceof Template\Interfaces\TemplateInterface)
            );
        }

        /**
         * Lazy initialization of the Headers dependency
         * @param \Brickoo\Http\Component\Interfaces\HeadersInterface $Headers the Headers dependency to inject
         * @return \Brickoo\Http\Component\Interfaces\HeadersInterface
         */
        public function Headers(\Brickoo\Http\Component\Interfaces\HeadersInterface $Headers = null)
        {
            return $this->getDependency(
                'Headers',
                '\Brickoo\Http\Component\Interfaces\HeadersInterface',
                function() {return new Component\Headers();},
                $Headers
            );
        }

        /**
         * Checks if a response header is set.
         * @param string $headerName the header name to check
         * @return boolean check result
         */
        public function hasHeader($headerName)
        {
            TypeValidator::IsString($headerName);

            return $this->Headers()->has($headerName);
        }

        /**
         * Sends the headers to the output buffer.
         * @param callable $callback this argument should be only used for test purposes
         * @return \Brickoo\Http\Response
         */
        public function sendHeaders($callback = null)
        {
            $function = (is_callable($callback) ? $callback : 'header');

            call_user_func(
                $function,
                sprintf("%s %d %s", $this->getProtocol(), $this->getStatusCode(), $this->getStatusPhrase())
            );

            $this->Headers()->rewind();
            while($this->Headers()->valid()) {
                call_user_func($function, sprintf("%s: %s", $this->Headers()->key(), $this->Headers()->current()));
                $this->Headers()->next();
            }

            return $this;
        }

        /**
         * Holds the protocol used by the response.
         * @var string
         */
        protected $protocol;

        /**
         * Returns then response protocol.
         * @return string the response protocol
         */
        public function getProtocol()
        {
            return $this->protocol;
        }

        /**
         * Sets the response protocol used.
         * @param string $protocol the response protocol
         * @return \Brickoo\Http\Response
         */
        public function setProtocol($protocol)
        {
            TypeValidator::IsString($protocol);
            TypeValidator::MatchesRegex('~^HTTP\/1\.[0|1]$~', $protocol);

            $this->protocol = $protocol;

            return $this;
        }

        /**
         * Holds the status code of the response;
         * @var integer
         */
        protected $statusCode;

        /**
         * Returns the status code of te response.
         * @return integer the status code
         */
        public function getStatusCode()
        {
            return $this->statusCode;
        }

        /**
         * Sets the status code of the response.
         * @param integer $statusCode the status code
         * @return \Brickoo\Http\Response
         */
        public function setStatusCode($statusCode)
        {
            TypeValidator::IsInteger($statusCode);

            $this->statusCode = $statusCode;

            return $this;
        }

        /**
         * Checks if the status code has the passed value.
         * If passing an array chek if the array contains the status code.
         * @param integer|array $statusCode the status code to check
         * @return boolean check result
         */
        public function hasStatusCode($statusCode)
        {
            if (is_array($statusCode)) {
                return in_array($this->getStatusCode(), $statusCode);
            }

            TypeValidator::IsInteger($statusCode);

            return ($this->getStatusCode() == $statusCode);
        }

        /**
         * Returns the status code phrase.
         * @param integer|null $statusCode the status code to return the phrase from
         * @throws Exceptions\StatusCodeUnknownException if the status code is unknowed
         * @return string the status code phrase
         */
        public function getStatusPhrase($statusCode = null)
        {
            if ($statusCode === null) {
                $statusCode = $this->getStatusCode();
            }

            if ((! is_int($statusCode)) || (! array_key_exists($statusCode, $this->statusPhrases))) {
                throw new Exceptions\StatusCodeUnknownException($statusCode);
            }

            return $this->statusPhrases[$statusCode];
        }

        /**
         * Sets or adds an status code and phrase to the knowed list.
         * @param integer $statusCode the status code to add or overwrite
         * @param string $statusPhrase the phrase to bind to the status code
         * @return \Brickoo\Http\Response
         */
        public function setStatusPhrase($statusCode, $statusPhrase)
        {
            TypeValidator::IsInteger($statusCode);
            TypeValidator::IsString($statusPhrase);

            $this->statusPhrases[$statusCode] = $statusPhrase;

            return $this;
        }

        /**
         * Holds the response content.
         * @var string
         */
        protected $content;

        /**
         * Returns the assigned response content.
         * @return string the response content
         */
        public function getContent()
        {
            if ($this->content === null && $this->hasTemplate()) {
                $this->content = $this->Template()->render();
            }
            return $this->content;
        }

        /**
         * Sets the response content to sent.
         * @param string $content the response content
         * @return \Brickoo\Http\Response
         */
        public function setContent($content)
        {
            TypeValidator::IsString($content);

            $this->content = $content;

            return $this;
        }

        /**
         * Sends the response content to the output buffer.
         * @return \Brickoo\Http\Response
         */
        public function sendContent()
        {
            echo (ltrim($this->getContent(), "\r\n"));

            return $this;
        }

        /**
         * Class cosntructor.
         * Initializes the class properties.
         * @return void
         */
        public function __construct()
        {
            $this->protocol      = 'HTTP/1.1';
            $this->statusCode    = 200;
        }

        /**
         * Sends the output to the output buffer.
         * @param callable $headersCallback this argument should be only used for test purposes
         * @return void
         */
        public function send($headersCallback = null)
        {
            $this->sendHeaders($headersCallback);
            $this->sendContent();

            return $this;
        }

        /**
         * Returns the converted response as a string.
         * @return string the converted response
         */
        public function toString()
        {
            $response  = sprintf("%s %d %s\r\n", $this->getProtocol(), $this->getStatusCode(), $this->getStatusPhrase());
            $response .= rtrim($this->Headers()->toString(), "\r\n");
            $response .= "\r\n\r\n" . $this->getContent();

            return $response;
        }

        /**
         * Converts the reponse to a string.
         * @return string the converted response
         */
        public function __toString()
        {
            return $this->toString();
        }

    }