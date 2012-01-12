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
     * Test suite for the Http Request class.
     * @see Brickoo\Library\Http\Request
     * @author Celestino Diaz <celestino.diaz@gmx.de>
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
         * Holds an instance of the Httprequest class.
         * @var Brickoo\Library\Http\Request
         */
        protected $HttpRequest;

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

            $this->HttpRequest = new Request();
        }

        /**
         * Test if the class can be created.
         * @covers Brickoo\Library\Http\Request::__construct
         */
        public function testHttpConstructor()
        {
            $this->assertInstanceOf
            (
                '\Brickoo\Library\Http\Interfaces\RequestInterface',
                $this->HttpRequest
            );
        }

        /**
         * Test if the Core\Request dependency with lazy initialization can be retrieved.
         * @covers Brickoo\Library\Http\Request::getCoreRequest
         * @covers Brickoo\Library\Http\Request::injectCoreRequest
         */
        public function testGetCoreRequest()
        {
            $this->assertInstanceOf
            (
                '\Brickoo\Library\Core\Interfaces\RequestInterface',
                $this->HttpRequest->getCoreRequest()
            );
        }

        /**
         * Test if the Core\Request can be injected and the HttpRequest reference is returned.
         * @covers Brickoo\Library\Http\Request::injectCoreRequest
         */
        public function testInjectCoreRequest()
        {
            $CoreRequestStub = $this->getRequestStub();
            $this->HttpRequest->injectCoreRequest($CoreRequestStub);
            $this->assertSame($CoreRequestStub, $this->HttpRequest->getCoreRequest());
            $this->assertAttributeSame($CoreRequestStub, 'CoreRequest', $this->HttpRequest);

            return $this->HttpRequest;
        }

        /**
         * Test if trying to overwrite the CoreRequest dependecy throws an exception.
         * @covers Brickoo\Library\Http\Request::injectCoreRequest
         * @covers Brickoo\Library\Core\Exceptions\DependencyOverwriteException
         * @expectedException Brickoo\Library\Core\Exceptions\DependencyOverwriteException
         * @depends testInjectCoreRequest
         */
        public function testInjectCoreRequestDependencyException($HttpRequest)
        {
            $HttpRequest->injectCoreRequest($this->getRequestStub());
        }

        /**
         * Test if the Url dependecy can be retrieved with lazy initialization.
         * @covers Brickoo\Library\Http\Request::Url
         * @covers Brickoo\Library\Http\Request::injectUrl
         */
        public function testGetUrl()
        {
            $this->assertInstanceOf('\Brickoo\Library\Http\Url', $this->HttpRequest->Url());
        }

        /**
         * Test if the Url can be injected and the HttpRequest reference is returned.
         * @covers Brickoo\Library\Http\Request::injectUrl
         */
        public function testInjectUrl()
        {
            $UrlMock = $this->getMock('\Brickoo\Library\Http\Interfaces\UrlInterface');
            $this->HttpRequest->injectUrl($UrlMock);
            $this->assertSame($UrlMock, $this->HttpRequest->Url());
            $this->assertAttributeSame($UrlMock, '_Url', $this->HttpRequest);

            return $this->HttpRequest;
        }

        /**
         * Test if trying to overwrite the Url dependecy throws an exception.
         * @covers Brickoo\Library\Http\Request::injectUrl
         * @covers Brickoo\Library\Core\Exceptions\DependencyOverwriteException
         * @expectedException Brickoo\Library\Core\Exceptions\DependencyOverwriteException
         * @depends testInjectUrl
         */
        public function testInjectUrlDependencyException($HttpRequest)
        {
            $HttpRequest->injectUrl($this->HttpRequest->Url());
        }

        /**
         * Test if the variables order are set.
         * @covers Brickoo\Library\Http\Request::getVariablesOrder
         */
        public function testGetVariablesOrder()
        {
            $order = array('G', 'P', 'C', 'F');
            $this->assertEquals($order, $this->HttpRequest->getVariablesOrder());
            $this->assertAttributeEquals($order, 'variablesOrder', $this->HttpRequest);
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
        }

        /**
         * Test is a wrong argument type throws an exception.
         * @covers Brickoo\Library\Http\Request::setVariablesOrder
         * @expectedException InvalidArgumentException
         */
        public function testSetVariablesOrderArgumentException()
        {
            $this->HttpRequest->setVariablesOrder(array());
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
        }

        /**
         * Test is a wrong argument type throws an exception.
         * @covers Brickoo\Library\Http\Request::getParam
         * @expectedException InvalidArgumentException
         */
        public function testGetParamArgumentException()
        {
            $this->HttpRequest->getParam(array());
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
        }

        /**
         * Test is a wrong argument type throws an exception.
         * @covers Brickoo\Library\Http\Request::getHttpHeader
         * @expectedException InvalidArgumentException
         */
        public function testGetHttpHeaderArgumentException()
        {
            $this->HttpRequest->getHttpHeader(array());
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
        }

        /**
         * Test is a wrong argument type throws an exception.
         * @covers Brickoo\Library\Http\Request::isHttpHeaderAvailable
         * @expectedException InvalidArgumentException
         */
        public function testIsHttpHeaderAvailableArgumentException()
        {
            $this->HttpRequest->isHttpHeaderAvailable(array());
        }

        /**
         * Test if http headers can be added and overwriten.
         * @covers Brickoo\Library\Http\Request::addHttpHeaders
         * @covers Brickoo\Library\Http\Request::isHttpHeaderAvailable
         * @covers Brickoo\Library\Http\Request::filterHeaders
         */
        public function testAddHttpHeaders()
        {
            $this->assertSame($this->HttpRequest, $this->HttpRequest->addHttpHeaders(array('NEW_VALUE' => 'something')));
            $this->assertEquals('something', $this->HttpRequest->getHttpHeader('NEW_VALUE'));
            $this->assertSame($this->HttpRequest, $this->HttpRequest->addHttpHeaders(array('UNIT_TEST' => 'value 2', null), true));
            $this->assertSame($this->HttpRequest, $this->HttpRequest->addHttpHeaders(array('UNIT_TEST' => 'value 3'), true));
            $this->assertEquals('value 3', $this->HttpRequest->getHttpHeader('UNIT_TEST'));
        }

        /**
         * Test if the accept types can be retrieved.
         * @covers Brickoo\Library\Http\Request::getAcceptTypes
         * @covers Brickoo\Library\Http\Request::getAcceptHeaderByRegex
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
        }

        /**
         * Test is a wrong argument type throws an exception.
         * @covers Brickoo\Library\Http\Request::isTypeSupported
         * @expectedException InvalidArgumentException
         */
        public function testIsTypeSupportedArgumentException()
        {
            $this->HttpRequest->isTypeSupported(null);
        }

        /**
         * Test if the accept languages can be retrieved.
         * @covers Brickoo\Library\Http\Request::getAcceptLanguages
         * @covers Brickoo\Library\Http\Request::getAcceptHeaderByRegex
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
        }

        /**
         * Test is a wrong argument type throws an exception.
         * @covers Brickoo\Library\Http\Request::isLanguageSupported
         * @expectedException InvalidArgumentException
         */
        public function testIsLanguageSupportedArgumentException()
        {
            $this->HttpRequest->isLanguageSupported(null);
        }

        /**
         * Test if the accept encodings can be retrieved.
         * @covers Brickoo\Library\Http\Request::getAcceptEncodings
         * @covers Brickoo\Library\Http\Request::getAcceptHeaderByRegex
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
        }

        /**
         * Test is a wrong argument type throws an exception.
         * @covers Brickoo\Library\Http\Request::isEncodingSupported
         * @expectedException InvalidArgumentException
         */
        public function testIsEncodingSupportedArgumentException()
        {
            $this->HttpRequest->isEncodingSupported(null);
        }

        /**
         * Test if the accept charsets can be retrieved.
         * @covers Brickoo\Library\Http\Request::getAcceptCharsets
         * @covers Brickoo\Library\Http\Request::getAcceptHeaderByRegex
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
         * @covers Brickoo\Library\Http\Request::getAcceptHeaderByRegex
         */
        public function testGetAcceptHeaderByRegex()
        {
            $this->assertArrayHasKey
            (
                'ISO-8859-1',
                $this->HttpRequest->getAcceptHeaderByRegex
                (
                    '~^(?<charset>[a-z0-9\-\*]+)\s*(\;\s*q\=(?<quality>(0\.\d{1,5}|1\.0|[01])))?$~i',
                    'charset',
                    $this->HttpRequest->getHTTPHeader('Accept.Charset')
                )
            );
        }

        /**
         * Test if the request method is returned, trying to cover the modified hacked header.
         * @covers Brickoo\Library\Http\Request::getRequestMethod
         */
        public function testGetHttpMethod()
        {
            $_SERVER['REQUEST_METHOD'] = 'LOCAL';
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
            $map = array(
                array('X.Forwarded.Proto', null),
                array('ssl.https', null),
                array('https', null, 1)
            );
            $RequestStub = $this->getRequestStub(array('getServerVar'));
            $RequestStub->expects($this->exactly(3))
                        ->method('getServerVar')
                        ->will($this->returnValueMap($map));

            $this->HttpRequest->injectCoreRequest($RequestStub);

            $this->assertTrue($this->HttpRequest->isSecureConnection());
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
            $RequestStub->expects($this->once())
                        ->method('getServerVar')
                        ->will($this->returnValueMap($map));

            $this->HttpRequest->injectCoreRequest($RequestStub);

            $this->assertTrue($this->HttpRequest->isSecureConnection());
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
            $RequestStub->expects($this->once())
                        ->method('getServerVar')
                        ->will($this->returnValueMap($map));

            $this->HttpRequest->injectCoreRequest($RequestStub);

            $this->assertTrue($this->HttpRequest->isAjaxRequest());
        }

        /**
         * Test if the request path can be retrieved.
         * @covers Brickoo\Library\Http\Request::getRequestPath
         */
        public function testGetRequestPath()
        {
            $UrlStub = $this->getMock('\Brickoo\Library\Http\Url', array('getRequestPath'));
            $UrlStub->expects($this->once())
                    ->method('getRequestPath')
                    ->will($this->returnValue('/path/used'));
            $this->HttpRequest->injectUrl($UrlStub);

            $this->assertEquals('/path/used', $this->HttpRequest->getRequestPath());
        }

        /**
         * Test if the hostname can be retrieved.
         * @covers Brickoo\Library\Http\Request::getHostname
         */
        public function testGetHostname()
        {
            $UrlStub = $this->getMock('\Brickoo\Library\Http\Url', array('getHost'));
            $UrlStub->expects($this->once())
                    ->method('getHost')
                    ->will($this->returnValue('localhost'));
            $this->HttpRequest->injectUrl($UrlStub);

            $this->assertEquals('localhost', $this->HttpRequest->getHostname());
        }

    }

?>