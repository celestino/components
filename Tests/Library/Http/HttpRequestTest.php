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
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     * @version $Id: HttpRequestTest.php 16 2011-12-23 22:39:50Z celestino $
     */

    class HttpRequestTest extends PHPUnit_Framework_TestCase
    {

        /**
         * Returns an Core Request Stub for the Http Request to look up for server variables.
         * @param array $requestMethods the request methods
         * @return object implementing the Brickoo\Library\Core\Interfaces\Request
         */
        protected function getRequestStub(array $requestMethods = null)
        {
            return $this->getMock
            (
                'Brickoo\Library\Core\Request',
                ($requestMethods === null ? null : array_values($requestMethods))
            );
        }

        /**
         * Set up the environment.
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
        }

        /**
         * Test if the class can be created.
         * @covers Brickoo\Library\Http\Request::__construct
         * @covers Brickoo\Library\Http\Request::clear
         */
        public function testHttpConstructor()
        {
            $this->assertInstanceOf
            (
                '\Brickoo\Library\Http\Interfaces\RequestInterface',
                new Request($this->getRequestStub())
            );
        }

        /**
         * Test if the Url support can be retrieved.
         * @covers Brickoo\Library\Http\Request::Url
         * @covers Brickoo\Library\Http\Request::injectUrl
         */
        public function testGetUrlObject()
        {
            $HttpRequest = new Request($this->getRequestStub());
            $this->assertInstanceOf('\Brickoo\Library\Http\Url', $HttpRequest->Url());
        }

        /**
         * Test if the Url passed support can be assigned
         * @covers Brickoo\Library\Http\Request::injectUrl
         */
        public function testSetUrlPassedSupport()
        {
            $UrlMock = $this->getMock('\Brickoo\Library\Http\Interfaces\UrlInterface');
            $HttpRequest = new Request($this->getRequestStub());
            $HttpRequest->injectUrl($UrlMock);
            $this->assertSame($UrlMock, $HttpRequest->Url());
        }

        /**
         * Test if trying to override the Url object throws an exception.
         * @covers Brickoo\Library\Http\Request::injectUrl
         * @covers Brickoo\Library\Core\Exceptions\DependencyOverrideException
         * @expectedException Brickoo\Library\Core\Exceptions\DependencyOverrideException
         */
        public function testAddUrlDependencyException()
        {
            $HttpRequest = new Request($this->getRequestStub());
            $Url = $HttpRequest->Url();
            $UrlMock = $this->getMock('\Brickoo\Library\Http\Interfaces\UrlInterface');
            $HttpRequest->injectUrl($UrlMock);
        }

        /**
         * Test if the variables order are set.
         * @covers Brickoo\Library\Http\Request::getVariablesOrder
         */
        public function testGetVariablesOrder()
        {
            $HttpRequest = new Request($this->getRequestStub());
            $this->assertContainsOnly('string', $HttpRequest->getVariablesOrder());
            $this->assertEquals(array('G', 'P', 'C', 'F'), $HttpRequest->getVariablesOrder());
        }

        /**
         * Test if the variables order can be set.
         * @covers Brickoo\Library\Http\Request::setVariablesOrder
         * @covers Brickoo\Library\Http\Request::filterOrderChars
         */
        public function testSetVariablesOrder()
        {
            $HttpRequest = new Request($this->getRequestStub());
            $this->assertSame($HttpRequest, $HttpRequest->setVariablesOrder('g'));
            $this->assertSame($HttpRequest, $HttpRequest->setVariablesOrder('GpC'));

            $this->assertSame($HttpRequest, $HttpRequest->setVariablesOrder('ggg'));
            $this->assertContains('G', $HttpRequest->getVariablesOrder());
            $this->assertEquals(array('G'), $HttpRequest->getVariablesOrder());
        }

        /**
         * Test is a wrong argument type throws an exception.
         * @covers Brickoo\Library\Http\Request::setVariablesOrder
         * @expectedException InvalidArgumentException
         */
        public function testSetVariablesOrderArgumentException()
        {
            $HttpRequest = new Request($this->getRequestStub());
            $HttpRequest->setVariablesOrder(array());
        }

        /**
         * Test if the params are returned.
         * @covers Brickoo\Library\Http\Request::getParams
         * @covers Brickoo\Library\Http\Request::collectParams
         */
        public function testGetParams()
        {
            $HttpRequest = new Request($this->getRequestStub());
            $this->assertInternalType('array', $HttpRequest->getParams());
            $this->assertArrayHasKey('UNIT_TEST', $HttpRequest->getParams());
        }

        /**
         * Test if the specific param is returned.
         * @covers Brickoo\Library\Http\Request::getParam
         * @covers Brickoo\Library\Http\Request::collectParams
         */
        public function testGetParam()
        {
            $HttpRequest = new Request($this->getRequestStub());
            $this->assertEquals('HttpTest', $HttpRequest->getParam('UNIT_TEST'));
            $this->assertEquals(null, $HttpRequest->getParam('unit_test'));
            $this->assertEquals('defaultValue', $HttpRequest->getParam('doesNotExist', 'defaultValue'));
        }

        /**
         * Test is a wrong argument type throws an exception.
         * @covers Brickoo\Library\Http\Request::getParam
         * @expectedException InvalidArgumentException
         */
        public function testGetParamArgumentException()
        {
            $HttpRequest = new Request($this->getRequestStub());
            $HttpRequest->getParam(array());
        }

        /**
         * Test if the http headers are returned.
         * @covers Brickoo\Library\Http\Request::getHttpHeaders
         * @covers Brickoo\Library\Http\Request::collectHttpHeaders
         * @covers Brickoo\Library\Http\Request::collectApacheHeaders
         */
        public function testGetHttpHeaders()
        {
            $HttpRequest = new Request($this->getRequestStub());
            $this->assertInternalType('array', $HttpRequest->getHttpHeaders());
            $this->assertArrayHasKey('UNIT_TEST', $HttpRequest->getHttpHeaders());
        }

        /**
         * Test if the http header ist returned.
         * @covers Brickoo\Library\Http\Request::getHttpHeader
         * @covers Brickoo\Library\Http\Request::collectHttpHeaders
         */
        public function testGetHttpHeader()
        {
            $HttpRequest = new Request($this->getRequestStub());
            $this->assertEquals('HttpTest', $HttpRequest->getHttpHeader('UNIT_TEST'));
            $this->assertEquals('HttpTest', $HttpRequest->getHttpHeader('unit test'));
            $this->assertEquals('HttpTest', $HttpRequest->getHttpHeader('unit.test'));
            $this->assertEquals('HttpTest', $HttpRequest->getHttpHeader('unit-test'));
            $this->assertEquals('defaultValue', $HttpRequest->getHttpHeader('doesNotExist', 'defaultValue'));
        }

        /**
         * Test is a wrong argument type throws an exception.
         * @covers Brickoo\Library\Http\Request::getHttpHeader
         * @expectedException InvalidArgumentException
         */
        public function testGetHttpHeaderArgumentException()
        {
            $HttpRequest = new Request($this->getRequestStub());
            $HttpRequest->getHttpHeader(array());
        }

        /**
         * Test if the http header can be checked.
         * @covers Brickoo\Library\Http\Request::isHttpHeaderAvailable
         * @covers Brickoo\Library\Http\Request::collectHttpHeaders
         */
        public function testIsHttpHeaderAvailable()
        {
            $HttpRequest = new Request($this->getRequestStub());
            $this->assertTrue($HttpRequest->isHttpHeaderAvailable('UNIT_TEST'));
            $this->assertTrue($HttpRequest->isHttpHeaderAvailable('unit test'));
            $this->assertTrue($HttpRequest->isHttpHeaderAvailable('unit.test'));
            $this->assertTrue($HttpRequest->isHttpHeaderAvailable('unit-test'));
        }

        /**
         * Test is a wrong argument type throws an exception.
         * @covers Brickoo\Library\Http\Request::isHttpHeaderAvailable
         * @expectedException InvalidArgumentException
         */
        public function testIsHttpHeaderAvailableArgumentException()
        {
            $HttpRequest = new Request($this->getRequestStub());
            $HttpRequest->isHttpHeaderAvailable(array());
        }

        /**
         * Test if http headers can be added and overridden.
         * @covers Brickoo\Library\Http\Request::addHttpHeaders
         * @covers Brickoo\Library\Http\Request::isHttpHeaderAvailable
         * @covers Brickoo\Library\Http\Request::filterHeaders
         */
        public function testAddHttpHeaders()
        {
            $HttpRequest = new Request($this->getRequestStub());
            $this->assertSame($HttpRequest, $HttpRequest->addHttpHeaders(array('NEW_VALUE' => 'something')));
            $this->assertEquals('something', $HttpRequest->getHttpHeader('NEW_VALUE'));
            $this->assertSame($HttpRequest, $HttpRequest->addHttpHeaders(array('UNIT_TEST' => 'value 2', null), true));
            $this->assertSame($HttpRequest, $HttpRequest->addHttpHeaders(array('UNIT_TEST' => 'value 3'), true));
            $this->assertEquals('value 3', $HttpRequest->getHttpHeader('UNIT_TEST'));
        }

        /**
         * Test is a wrong argument type throws an exception.
         * @covers Brickoo\Library\Http\Request::addHttpHeaders
         * @expectedException InvalidArgumentException
         */
        public function testAddHttpHeadersArgumentException()
        {
            $HttpRequest = new Request($this->getRequestStub());
            $HttpRequest->addHttpHeaders(array());
        }

        /**
         * Test if the accept types can be retrieved.
         * @covers Brickoo\Library\Http\Request::getAcceptTypes
         * @covers Brickoo\Library\Http\Request::getAcceptHeaderByRegex
         */
        public function testGetAcceptTypes()
        {
            $HttpRequest = new Request($this->getRequestStub());
            $this->assertInternalType('array', $HttpRequest->getAcceptTypes());
            $this->assertArrayHasKey('application/xml', $HttpRequest->getAcceptTypes());
            $this->assertArrayHasKey('text/html', $HttpRequest->getAcceptTypes());
            $this->assertArrayHasKey('application/xhtml+xml', $HttpRequest->getAcceptTypes());
            $this->assertArrayHasKey('*/*', $HttpRequest->getAcceptTypes());
        }

        /**
         * Test if the types can be recognized as supported.
         * @covers Brickoo\Library\Http\Request::isTypeSupported
         */
        public function testIsTypeSupported()
        {
            $HttpRequest = new Request($this->getRequestStub());
            $this->assertTrue($HttpRequest->isTypeSupported('application/xml'));
            $this->assertTrue($HttpRequest->isTypeSupported('text/html'));
            $this->assertTrue($HttpRequest->isTypeSupported('*/*'));
        }

        /**
         * Test is a wrong argument type throws an exception.
         * @covers Brickoo\Library\Http\Request::isTypeSupported
         * @expectedException InvalidArgumentException
         */
        public function testIsTypeSupportedArgumentException()
        {
            $HttpRequest = new Request($this->getRequestStub());
            $HttpRequest->isTypeSupported(null);
        }

        /**
         * Test if the accept languages can be retrieved.
         * @covers Brickoo\Library\Http\Request::getAcceptLanguages
         * @covers Brickoo\Library\Http\Request::getAcceptHeaderByRegex
         */
        public function testGetAcceptLanguages()
        {
            $HttpRequest = new Request($this->getRequestStub());
            $this->assertInternalType('array', $HttpRequest->getAcceptLanguages());
            $this->assertArrayHasKey('de-DE', $HttpRequest->getAcceptLanguages());
            $this->assertArrayHasKey('de', $HttpRequest->getAcceptLanguages());
            $this->assertArrayHasKey('en-US', $HttpRequest->getAcceptLanguages());
            $this->assertArrayHasKey('en', $HttpRequest->getAcceptLanguages());
        }

        /**
         * Test if the language can be recognized as supported.
         * @covers Brickoo\Library\Http\Request::isLanguageSupported
         */
        public function testIsLanguageSupported()
        {
            $HttpRequest = new Request($this->getRequestStub());
            $this->assertTrue($HttpRequest->isLanguageSupported('de-DE'));
            $this->assertTrue($HttpRequest->isLanguageSupported('de'));
            $this->assertTrue($HttpRequest->isLanguageSupported('en-US'));
            $this->assertTrue($HttpRequest->isLanguageSupported('en'));
        }

        /**
         * Test is a wrong argument type throws an exception.
         * @covers Brickoo\Library\Http\Request::isLanguageSupported
         * @expectedException InvalidArgumentException
         */
        public function testIsLanguageSupportedArgumentException()
        {
            $HttpRequest = new Request($this->getRequestStub());
            $HttpRequest->isLanguageSupported(null);
        }

        /**
         * Test if the accept encodings can be retrieved.
         * @covers Brickoo\Library\Http\Request::getAcceptEncodings
         * @covers Brickoo\Library\Http\Request::getAcceptHeaderByRegex
         */
        public function testGetAcceptEncodings()
        {
            $HttpRequest = new Request($this->getRequestStub());
            $this->assertInternalType('array', $HttpRequest->getAcceptEncodings());
            $this->assertArrayHasKey('gzip', $HttpRequest->getAcceptEncodings());
            $this->assertArrayHasKey('deflate', $HttpRequest->getAcceptEncodings());
            $this->assertArrayHasKey('sdch', $HttpRequest->getAcceptEncodings());
        }

        /**
         * Test if the encoding can be recognized as supported.
         * @covers Brickoo\Library\Http\Request::isEncodingSupported
         */
        public function testIsEncodingSupported()
        {
            $HttpRequest = new Request($this->getRequestStub());
            $this->assertTrue($HttpRequest->isEncodingSupported('deflate'));
            $this->assertTrue($HttpRequest->isEncodingSupported('gzip'));
            $this->assertTrue($HttpRequest->isEncodingSupported('sdch'));
        }

        /**
         * Test is a wrong argument type throws an exception.
         * @covers Brickoo\Library\Http\Request::isEncodingSupported
         * @expectedException InvalidArgumentException
         */
        public function testIsEncodingSupportedArgumentException()
        {
            $HttpRequest = new Request($this->getRequestStub());
            $HttpRequest->isEncodingSupported(null);
        }

        /**
         * Test if the accept charsets can be retrieved.
         * @covers Brickoo\Library\Http\Request::getAcceptCharsets
         * @covers Brickoo\Library\Http\Request::getAcceptHeaderByRegex
         */
        public function testGetAcceptCharsets()
        {
            $HttpRequest = new Request($this->getRequestStub());
            $this->assertInternalType('array', $HttpRequest->getAcceptCharsets());
            $this->assertArrayHasKey('ISO-8859-1', $HttpRequest->getAcceptCharsets());
            $this->assertArrayHasKey('utf-8', $HttpRequest->getAcceptCharsets());
            $this->assertArrayHasKey('*', $HttpRequest->getAcceptCharsets());
        }

        /**
         * Test if the charset can be recognized as supported.
         * @covers Brickoo\Library\Http\Request::isCharsetSupported
         */
        public function testIsCharsetSupported()
        {
            $HttpRequest = new Request($this->getRequestStub());
            $this->assertTrue($HttpRequest->isCharsetSupported('ISO-8859-1'));
            $this->assertTrue($HttpRequest->isCharsetSupported('utf-8'));
            $this->assertTrue($HttpRequest->isCharsetSupported('*'));
        }

        /**
         * Test if passing an wrong type it throws an exception.
         * @covers Brickoo\Library\Http\Request::isCharsetSupported
         * @expectedException InvalidArgumentException
         */
        public function testIsCharsetSupportedArgumentException()
        {
            $HttpRequest = new Request($this->getRequestStub());
            $HttpRequest->isCharsetSupported(array('wrongType'));
        }

        /**
         * Test if the parameters are validated.
         * @covers Brickoo\Library\Http\Request::getAcceptHeaderByRegex
         */
        public function testGetAcceptHeaderByRegex()
        {
            $HttpRequest = new Request($this->getRequestStub());
            $this->assertArrayHasKey
            (
                'ISO-8859-1',
                $HttpRequest->getAcceptHeaderByRegex
                (
                    '~^(?<charset>[a-z0-9\-\*]+)\s*(\;\s*q\=(?<quality>(0\.\d{1,5}|1\.0|[01])))?$~i',
                    'charset',
                    $HttpRequest->getHTTPHeader('Accept.Charset')
                )
            );
        }

        /**
         * Test if the request method is returned.
         * @covers Brickoo\Library\Http\Request::getRequestMethod
         */
        public function testGetHttpMethod()
        {
            $HttpRequest = new Request($this->getRequestStub());
            $this->assertEquals('GET', $HttpRequest->getRequestMethod());
        }

        /**
         * Test if the raw body can be retrieved.
         * This can really be tested du the input can be overriden.
         * @covers Brickoo\Library\Http\Request::getRawBody
         */
        public function testGetRawBody()
        {
            $HttpRequest = new Request($this->getRequestStub());
            $this->assertInternalType('string', $HttpRequest->getRawBody());
        }

        /**
         * Test if the https mode is recognized.
         * @covers Brickoo\Library\Http\Request::isSecureConnection
         */
        public function testIsSecureConnection()
        {
            $map = array(
                array('X.Forwarded.Proto', null),
                array('ssl.https', null),
                array('https', null, 1)
            );
            $RequestStub = $this->getRequestStub(array('getServerVar'));
            $RequestStub->expects($this->exactly(3))
                        ->method('getServerVar')
                        ->will($this->returnValueMap($map));
            $HttpRequest = new Request($RequestStub);

            $this->assertTrue($HttpRequest->isSecureConnection());
        }

        /**
         * Test if the https mode forwarded is recognized.
         * @covers Brickoo\Library\Http\Request::isSecureConnection
         */
        public function testIsSecureConnectionForwarded()
        {
            $map = array(
                array('X.Forwarded.Proto', null, 'https')
            );
            $RequestStub = $this->getRequestStub(array('getServerVar'));
            $RequestStub->expects($this->exactly(1))
                        ->method('getServerVar')
                        ->will($this->returnValueMap($map));
            $HttpRequest = new Request($RequestStub);

            $this->assertTrue($HttpRequest->isSecureConnection());
        }

        /**
         * Test if the Ajax header is recognized.
         * @covers Brickoo\Library\Http\Request::isAjaxRequest
         */
        public function testisAjaxRequest()
        {
            $map = array(
                array('X.Requested.With', null, 'XMLHttpRequest')
            );
            $RequestStub = $this->getRequestStub(array('getServerVar'));
            $RequestStub->expects($this->exactly(1))
                        ->method('getServerVar')
                        ->will($this->returnValueMap($map));
            $HttpRequest = new Request($RequestStub);

            $this->assertTrue($HttpRequest->isAjaxRequest());
        }

    }

?>