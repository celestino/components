<?php

    /*
     * Copyright (c) 2008-2011, Celestino Diaz Teran <celestino@users.sourceforge.net>.
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

    use Brickoo\Library\Core;
    use Brickoo\Library\Http\Request;
    use Brickoo\Library\Http\Url;


    // require PHPUnit Autoloader
    require_once ('PHPUnit/Autoload.php');

    /**
     * Fixture for the apache function.
     * @return array sample header values
     *
     */
    function apache_request_headers()
    {
        return array('APACHE_HEADER_NAME' => 'APACHE');
    }

    /**
     * HttpRequestTest
     *
     * Test case for the Http Request class.
     * @see Brickoo\Library\Http\Request
     * @author Celestino Diaz Teran <celestino@users.sourceforge.net>
     * @version $Id: $
     */

    class HttpRequestTest extends PHPUnit_Framework_TestCase
    {

        /**
         * Holds an instance of the HttpRequest class.
         * @var object HttpRequest
         */
        public $HttpRequest;

        /**
         * Set up the HttpRequest object used.
         * @return void
         */
        public function setUp()
        {
            $_GET['UNIT_TEST'] = 'HttpTest';
            $_SERVER['HTTP_UNIT_TEST'] = 'HttpTest';
            $_SERVER['HTTP_ACCEPT'] = 'text/html,application/xhtml+xml,application/xml   ;    q=0.9,*/*;q=0.8';
            $_SERVER['HTTP_ACCEPT_LANGUAGE'] = 'de-DE, de    ;    q=0.8,en-US;q=0.6,en;q=0.4';
            $_SERVER['HTTP_ACCEPT_ENCODING'] = 'gzip,deflate,sdch    ; q=0.1';
            $_SERVER['HTTP_ACCEPT_CHARSET'] = 'ISO-8859-1,utf-8    ;    q=0.7,*;q=0.3';
            $_SERVER['REQUEST_METHOD'] = 'GET';

            $this->HttpRequest = new Request(new Core\Request);
        }

        /**
         * Test if the class can be created.
         * @covers Brickoo\Library\Http\Request::__construct
         * @covers Brickoo\Library\Http\Request::clear
         * @covers Brickoo\Library\Http\Interfaces\HttpRequestInterface
         */
        public function testHttpConstructor()
        {
            $this->assertInstanceOf
            (
                '\Brickoo\Library\Http\Request',
                $this->HttpRequest
            );

            $HttpRequestMock = $this->getMock('\Brickoo\Library\Http\Interfaces\HttpRequestInterface');
            $this->assertInstanceOf
            (
                '\Brickoo\Library\Http\Interfaces\HttpRequestInterface',
                $HttpRequestMock
            );
        }

        /**
         * Test if the Url support can be retrieved.
         * @covers Brickoo\Library\Http\Request::Url
         * @covers Brickoo\Library\Http\Request::addUrlSupport
         */
        public function testGetUrlObject()
        {
            $this->assertInstanceOf('\Brickoo\Library\Http\Url', $this->HttpRequest->Url());
        }

        /**
         * Test if the Url passed support can be assigned
         * @covers Brickoo\Library\Http\Request::addUrlSupport
         */
        public function testAddUrlPassedSupport()
        {
            $TestUrl = new Url(new Core\Request);
            $this->HttpRequest->AddUrlSupport($TestUrl);
            $this->assertSame($TestUrl, $this->HttpRequest->Url());
        }

        /**
         * Test if trying to override the Url object throws an exception.
         * @covers Brickoo\Library\Http\Request::addUrlSupport
         * @expectedException LogicException
         */
        public function testAddUrlSupportLogicException()
        {
            $Url = $this->HttpRequest->Url();
            $this->HttpRequest->AddUrlSupport(new Url(new Core\Request));
        }

        /**
         * Test if the variables order are set.
         * @covers Brickoo\Library\Http\Request::getVariablesOrder
         */
        public function testGetVariablesOrder()
        {
            $this->assertContainsOnly('string', $this->HttpRequest->getVariablesOrder());
            $this->assertEquals(array('G', 'P', 'C'), $this->HttpRequest->getVariablesOrder());
        }

        /**
         * Test if the variables order can be set.
         * @covers Brickoo\Library\Http\Request::setVariablesOrder
         * @covers Brickoo\Library\Http\Request::filterOrderChars
         */
        public function testSetVariablesOrder()
        {
            $this->assertSame($this->HttpRequest, $this->HttpRequest->setVariablesOrder('g'));
            $this->assertSame($this->HttpRequest, $this->HttpRequest->setVariablesOrder('GpC'));

            $this->assertSame($this->HttpRequest, $this->HttpRequest->setVariablesOrder('ggg'));
            $this->assertContains('G', $this->HttpRequest->getVariablesOrder());
            $this->assertEquals(array('G'), $this->HttpRequest->getVariablesOrder());

            /*
            $this->assertFalse($this->HttpRequest->setVariablesOrder('EEEEEEEE'));
            $this->assertFalse($this->HttpRequest->setVariablesOrder(array('wronType')));
            */
        }

        /**
         * Test if the params are returned.
         * @covers Brickoo\Library\Http\Request::getParams
         * @covers Brickoo\Library\Http\Request::collectParams
         */
        public function testGetParams()
        {
            $this->assertInternalType('array', $this->HttpRequest->getParams());
            $this->assertArrayHasKey('UNIT_TEST', $this->HttpRequest->getParams());
        }

        /**
         * Test if the specific param is returned.
         * @covers Brickoo\Library\Http\Request::getParam
         * @covers Brickoo\Library\Http\Request::collectParams
         */
        public function testGetParam()
        {
            $this->assertEquals('HttpTest', $this->HttpRequest->getParam('UNIT_TEST'));
            $this->assertEquals(null, $this->HttpRequest->getParam('unit_test'));
            $this->assertEquals('defaultValue', $this->HttpRequest->getParam('doesNotExist', 'defaultValue'));

            /*
            $this->assertFalse($this->HttpRequest->getParam(array('wrongType')));
            */
        }

        /**
         * Test if the http headers are returned.
         * @covers Brickoo\Library\Http\Request::getHttpHeaders
         * @covers Brickoo\Library\Http\Request::collectHttpHeaders
         * @covers Brickoo\Library\Http\Request::collectApacheHeaders
         */
        public function testGetHttpHeaders()
        {
            $this->assertInternalType('array', $this->HttpRequest->getHttpHeaders());
            $this->assertArrayHasKey('UNIT_TEST', $this->HttpRequest->getHttpHeaders());
        }

        /**
         * Test if the http header ist returned.
         * @covers Brickoo\Library\Http\Request::getHttpHeader
         * @covers Brickoo\Library\Http\Request::collectHttpHeaders
         */
        public function testGetHttpHeader()
        {
            $this->assertEquals('HttpTest', $this->HttpRequest->getHttpHeader('UNIT_TEST'));
            $this->assertEquals('HttpTest', $this->HttpRequest->getHttpHeader('unit test'));
            $this->assertEquals('HttpTest', $this->HttpRequest->getHttpHeader('unit.test'));
            $this->assertEquals('HttpTest', $this->HttpRequest->getHttpHeader('unit-test'));
            $this->assertEquals('defaultValue', $this->HttpRequest->getHttpHeader('doesNotExist', 'defaultValue'));

            /*
            $this->assertFalse($this->HttpRequest->getHttpHeader(null));
            $this->assertFalse($this->HttpRequest->getHttpHeader(array('wrongType')));
            */
        }

        /**
         * Test if the http header can be checked.
         * @covers Brickoo\Library\Http\Request::isHttpHeaderAvailable
         * @covers Brickoo\Library\Http\Request::collectHttpHeaders
         */
        public function testIsHttpHeaderAvailable()
        {
            $this->assertTrue($this->HttpRequest->isHttpHeaderAvailable('UNIT_TEST'));
            $this->assertTrue($this->HttpRequest->isHttpHeaderAvailable('unit test'));
            $this->assertTrue($this->HttpRequest->isHttpHeaderAvailable('unit.test'));
            $this->assertTrue($this->HttpRequest->isHttpHeaderAvailable('unit-test'));

            /*
            $this->assertFalse($this->HttpRequest->isHttpHeaderAvailable(null));
            $this->assertFalse($this->HttpRequest->isHttpHeaderAvailable(array('wrongType')));
            */
        }

        /**
         * Test if http headers can be added and overridden.
         * @covers Brickoo\Library\Http\Request::addHttpHeaders
         * @covers Brickoo\Library\Http\Request::isHttpHeaderAvailable
         * @covers Brickoo\Library\Http\Request::cleanHeadersToAdd
         */
        public function testAddHttpHeaders()
        {
            $this->assertSame($this->HttpRequest, $this->HttpRequest->addHttpHeaders(array('NEW_VALUE' => 'something')));
            $this->assertEquals('something', $this->HttpRequest->getHttpHeader('NEW_VALUE'));
            $this->assertSame($this->HttpRequest, $this->HttpRequest->addHttpHeaders(array('UNIT_TEST' => 'value 2', null), true));
            $this->assertSame($this->HttpRequest, $this->HttpRequest->addHttpHeaders(array('UNIT_TEST' => 'value 3'), true));
            $this->assertEquals('value 3', $this->HttpRequest->getHttpHeader('UNIT_TEST'));

            /*
            $this->assertFalse($this->HttpRequest->addHttpHeaders(array()));
            $this->assertFalse($this->HttpRequest->addHttpHeaders(array(null)));
            $this->assertFalse($this->HttpRequest->addHttpHeaders(array('UNIT_TEST' => 'value')));
            $this->assertFalse($this->HttpRequest->addHttpHeaders(array('UNIT_TEST' => 'value', null)));
            $this->assertFalse($this->HttpRequest->addHttpHeaders(array(12345 => 'wrontType')));
            */
        }

        /**
         * Test if the accept types can be retrieved.
         * @covers Brickoo\Library\Http\Request::getAcceptTypes
         * @covers Brickoo\Library\Http\Request::getAcceptHeaderContentByRegex
         */
        public function testGetAcceptTypes()
        {
            $this->assertInternalType('array', $this->HttpRequest->getAcceptTypes());
            $this->assertArrayHasKey('application/xml', $this->HttpRequest->getAcceptTypes());
            $this->assertArrayHasKey('text/html', $this->HttpRequest->getAcceptTypes());
            $this->assertArrayHasKey('application/xhtml+xml', $this->HttpRequest->getAcceptTypes());
            $this->assertArrayHasKey('*/*', $this->HttpRequest->getAcceptTypes());
        }

        /**
         * Test if the types can be recognized as supported.
         * @covers Brickoo\Library\Http\Request::isTypeSupported
         */
        public function testIsTypeSupported()
        {
            $this->assertTrue($this->HttpRequest->isTypeSupported('application/xml'));
            $this->assertTrue($this->HttpRequest->isTypeSupported('text/html'));
            $this->assertTrue($this->HttpRequest->isTypeSupported('*/*'));

            /*
            $this->assertFalse($this->HttpRequest->isTypeSupported(null));
            $this->assertFalse($this->HttpRequest->isTypeSupported(array('wrongType')));
            $this->assertFalse($this->HttpRequest->isTypeSupported('bu11sh1t'));
            */
        }

        /**
         * Test if the accept languages can be retrieved.
         * @covers Brickoo\Library\Http\Request::getAcceptLanguages
         * @covers Brickoo\Library\Http\Request::getAcceptHeaderContentByRegex
         */
        public function testGetAcceptLanguages()
        {
            $this->assertInternalType('array', $this->HttpRequest->getAcceptLanguages());
            $this->assertArrayHasKey('de-DE', $this->HttpRequest->getAcceptLanguages());
            $this->assertArrayHasKey('de', $this->HttpRequest->getAcceptLanguages());
            $this->assertArrayHasKey('en-US', $this->HttpRequest->getAcceptLanguages());
            $this->assertArrayHasKey('en', $this->HttpRequest->getAcceptLanguages());
        }

        /**
         * Test if the language can be recognized as supported.
         * @covers Brickoo\Library\Http\Request::isLanguageSupported
         */
        public function testIsLanguageSupported()
        {
            $this->assertTrue($this->HttpRequest->isLanguageSupported('de-DE'));
            $this->assertTrue($this->HttpRequest->isLanguageSupported('de'));
            $this->assertTrue($this->HttpRequest->isLanguageSupported('en-US'));
            $this->assertTrue($this->HttpRequest->isLanguageSupported('en'));

            /*
            $this->assertFalse($this->HttpRequest->isLanguageSupported(null));
            $this->assertFalse($this->HttpRequest->isLanguageSupported(array('wrongType')));
            $this->assertFalse($this->HttpRequest->isLanguageSupported('bu11sh1t'));
            */
        }

        /**
         * Test if the accept encodings can be retrieved.
         * @covers Brickoo\Library\Http\Request::getAcceptEncodings
         * @covers Brickoo\Library\Http\Request::getAcceptHeaderContentByRegex
         */
        public function testGetAcceptEncodings()
        {
            $this->assertInternalType('array', $this->HttpRequest->getAcceptEncodings());
            $this->assertArrayHasKey('gzip', $this->HttpRequest->getAcceptEncodings());
            $this->assertArrayHasKey('deflate', $this->HttpRequest->getAcceptEncodings());
            $this->assertArrayHasKey('sdch', $this->HttpRequest->getAcceptEncodings());
        }

        /**
         * Test if the encoding can be recognized as supported.
         * @covers Brickoo\Library\Http\Request::isEncodingSupported
         */
        public function testIsEncodingSupported()
        {
            $this->assertTrue($this->HttpRequest->isEncodingSupported('deflate'));
            $this->assertTrue($this->HttpRequest->isEncodingSupported('gzip'));
            $this->assertTrue($this->HttpRequest->isEncodingSupported('sdch'));

            /*
            $this->assertFalse($this->HttpRequest->isEncodingSupported(null));
            $this->assertFalse($this->HttpRequest->isEncodingSupported(array('wrongType')));
            $this->assertFalse($this->HttpRequest->isEncodingSupported('bu11sh1t'));
            */
        }

        /**
         * Test if the accept charsets can be retrieved.
         * @covers Brickoo\Library\Http\Request::getAcceptCharsets
         * @covers Brickoo\Library\Http\Request::getAcceptHeaderContentByRegex
         */
        public function testGetAcceptCharsets()
        {
            $this->assertInternalType('array', $this->HttpRequest->getAcceptCharsets());
            $this->assertArrayHasKey('ISO-8859-1', $this->HttpRequest->getAcceptCharsets());
            $this->assertArrayHasKey('utf-8', $this->HttpRequest->getAcceptCharsets());
            $this->assertArrayHasKey('*', $this->HttpRequest->getAcceptCharsets());
        }

        /**
         * Test if the charset can be recognized as supported.
         * @covers Brickoo\Library\Http\Request::isCharsetSupported
         */
        public function testIsCharsetSupported()
        {
            $this->assertTrue($this->HttpRequest->isCharsetSupported('ISO-8859-1'));
            $this->assertTrue($this->HttpRequest->isCharsetSupported('utf-8'));
            $this->assertTrue($this->HttpRequest->isCharsetSupported('*'));
        }

        /**
         * Test if passing an wrong type it throws an exception.
         * @covers Brickoo\Library\Http\Request::isCharsetSupported
         * @expectedException InvalidArgumentException
         */
        public function testIsCharsetSupportedArgumentException()
        {
            $this->HttpRequest->isCharsetSupported(array('wrongType'));
        }

        /**
         * Test if the parameters are validated.
         * @covers Brickoo\Library\Http\Request::getAcceptHeaderContentByRegex
         */
        public function testGetAcceptHeaderContentByRegex()
        {
            $this->assertArrayHasKey
            (
                'ISO-8859-1',
                $this->HttpRequest->getAcceptHeaderContentByRegex
                (
                    '~^(?<charset>[a-z0-9\-\*]+)\s*(\;\s*q\=(?<quality>(0\.\d{1,5}|1\.0|[01])))?$~i',
                    'charset',
                    $this->HttpRequest->getHTTPHeader('Accept.Charset')
                )
            );
        }

        /**
         * Test if the request method is returned.
         * @covers Brickoo\Library\Http\Request::getRequestMethod
         */
        public function testGetHttpMethod()
        {
            $this->assertEquals('GET', $this->HttpRequest->getRequestMethod());
        }

        /**
         * Test if the raw body can be retrieved.
         * This can really be tested du the input can be overriden.
         * @covers Brickoo\Library\Http\Request::getRawBody
         */
        public function testGetRawBody()
        {
            $this->assertInternalType('string', $this->HttpRequest->getRawBody());
        }

        /**
         * Test if the https mode is recognized.
         * @covers Brickoo\Library\Http\Request::isSecureConnection
         */
        public function testIsSecureConnection()
        {
            $_SERVER['HTTPS'] = 1;
            $this->assertTrue($this->HttpRequest->isSecureConnection());
        }

        /**
         * Test if the https mode forwarded is recognized.
         * @covers Brickoo\Library\Http\Request::isSecureConnection
         */
        public function testIsSecureConnectionForwarded()
        {
            $_SERVER['X-FORWARDED-PROTO'] = 'https';
            $this->assertTrue($this->HttpRequest->isSecureConnection());
        }

        /**
         * Test if the Ajax header is recognized.
         * @covers Brickoo\Library\Http\Request::isAjaxRequest
         */
        public function testisAjaxRequest()
        {
            $_SERVER['X-Requested-With'] = 'XMLHttpRequest';
            $this->assertTrue($this->HttpRequest->isAjaxRequest());
        }

    }

?>