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

    use Brickoo\Http\Request;

    // require PHPUnit Autoloader
    require_once ('PHPUnit/Autoload.php');

    /**
     * RequestTest
     *
     * Test suite for the Request class.
     * @see Brickoo\Http\Request
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class RequestTest extends PHPUnit_Framework_TestCase
    {

        /**
         * Holds an instance of the Request.
         * @var \Brickoo\Http\Request
         */
        protected $Request;

        /**
         * Set up the Request instance used.
         * @return void
         */
        public function setUp()
        {
            $this->Request = new Request();
        }

        /**
         * Test if a Request instance can be created and implements the RequestInterface.
         * @covers Brickoo\Http\Request::__construct
         */
        public function testConstruct()
        {
            $this->assertInstanceOf
            (
                'Brickoo\Core\Interfaces\RequestInterface',
                ($Request = new Request())
            );
        }

        /**
         * Test if the Url dependency can be lazy initialized and stored.
         * @covers Brickoo\Http\Request::Url
         * @covers Brickoo\Http\Request::getDependency
         */
        public function testUrlLazyInitialization()
        {
            $this->assertInstanceOf
            (
                'Brickoo\Http\Component\Interfaces\UrlInterface',
                $this->Request->Url()
            );
            $this->assertAttributeContains($this->Request->Url(), 'dependencies', $this->Request);
        }

        /**
         * Test if the Url dependency inject is stored and the Request reference is returned.
         * @covers Brickoo\Http\Request::Url
         * @covers Brickoo\Http\Request::getDependency
         */
        public function testUrlInjection()
        {
            $Url = $this->getMock('\Brickoo\Http\Component\Interfaces\UrlInterface');
            $this->assertSame($this->Request, $this->Request->Url($Url));
            $this->assertAttributeContains($Url, 'dependencies', $this->Request);
        }

        /**
         * Test if the Headers dependency can be lazy initialized and stored.
         * @covers Brickoo\Http\Request::Headers
         * @covers Brickoo\Http\Request::getDependency
         */
        public function testHeadersLazyInitialization()
        {
            $this->assertInstanceOf
            (
                'Brickoo\Http\Component\Interfaces\HeadersInterface',
                $this->Request->Headers()
            );
            $this->assertAttributeContains($this->Request->Headers(), 'dependencies', $this->Request);
        }

        /**
         * Test if the Headers dependency inject is stored and the Request reference is returned.
         * @covers Brickoo\Http\Request::Headers
         * @covers Brickoo\Http\Request::getDependency
         */
        public function testHeadersInjection()
        {
            $Headers = $this->getMock('\Brickoo\Http\Component\Interfaces\HeadersInterface');
            $this->assertSame($this->Request, $this->Request->Headers($Headers));
            $this->assertAttributeContains($Headers, 'dependencies', $this->Request);
        }

        /**
         * Test if the Query dependency can be lazy initialized and stored.
         * @covers Brickoo\Http\Request::Query
         * @covers Brickoo\Http\Request::getDependency
         */
        public function testQueryLazyInitialization()
        {
            $this->assertInstanceOf
            (
                'Brickoo\Http\Component\Interfaces\QueryInterface',
                $this->Request->Query()
            );
            $this->assertAttributeContains($this->Request->Query(), 'dependencies', $this->Request);
        }

        /**
         * Test if the Query dependency inject is stored and the Request reference is returned.
         * @covers Brickoo\Http\Request::Query
         * @covers Brickoo\Http\Request::getDependency
         */
        public function testQueryInjection()
        {
            $Query = $this->getMock('\Brickoo\Http\Component\Interfaces\QueryInterface');
            $this->assertSame($this->Request, $this->Request->Query($Query));
            $this->assertAttributeContains($Query, 'dependencies', $this->Request);
        }

        /**
         * Test if the Post dependency can be lazy initialized and stored.
         * @covers Brickoo\Http\Request::Post
         * @covers Brickoo\Http\Request::getDependency
         */
        public function testPostLazyInitialization()
        {
            $this->assertInstanceOf
            (
                'Brickoo\Memory\Interfaces\ContainerInterface',
                $this->Request->Post()
            );
            $this->assertAttributeContains($this->Request->Post(), 'dependencies', $this->Request);
        }

        /**
         * Test if the Post dependency inject is stored and the Request reference is returned.
         * @covers Brickoo\Http\Request::Post
         * @covers Brickoo\Http\Request::getDependency
         */
        public function testPostInjection()
        {
            $Post = $this->getMock('\Brickoo\Memory\Interfaces\ContainerInterface');
            $this->assertSame($this->Request, $this->Request->Post($Post));
            $this->assertAttributeContains($Post, 'dependencies', $this->Request);
        }

        /**
         * Test if the Files dependency can be lazy initialized and stored.
         * @covers Brickoo\Http\Request::Files
         * @covers Brickoo\Http\Request::getDependency
         */
        public function testFilesLazyInitialization()
        {
            $this->assertInstanceOf
            (
                'Brickoo\Memory\Interfaces\ContainerInterface',
                $this->Request->Files()
            );
            $this->assertAttributeContains($this->Request->Files(), 'dependencies', $this->Request);
        }

        /**
         * Test if the Files dependency inject is stored and the Request reference is returned.
         * @covers Brickoo\Http\Request::Files
         * @covers Brickoo\Http\Request::getDependency
         */
        public function testFilesInjection()
        {
            $Files = $this->getMock('\Brickoo\Memory\Interfaces\ContainerInterface');
            $this->assertSame($this->Request, $this->Request->Files($Files));
            $this->assertAttributeContains($Files, 'dependencies', $this->Request);
        }

        /**
         * Test if setter and getter work together like expected.
         * @covers Brickoo\Http\Request::setProtocol
         * @covers Brickoo\Http\Request::getProtocol
         */
        public function testSetGetProtocol()
        {
            $this->assertSame($this->Request, $this->Request->setProtocol('HTTP/1.0'));
            $this->assertAttributeEquals('HTTP/1.0', 'protocol', $this->Request);
            $this->assertEquals('HTTP/1.0', $this->Request->getProtocol());
        }

        /**
         * Test if trying to use a wrong argument throws an exception.
         * @covers Brickoo\Http\Request::setProtocol
         * @expectedException InvalidArgumentException
         */
        public function testSetProtocolInvalidArgumentException()
        {
            $this->Request->setProtocol('HTTP/1.9');
        }

        /**
         * Test if the protocol can be retrieved from global variable.
         * @covers Brickoo\Http\Request::getProtocol
         */
        public function testGetProtocolFromGlobals()
        {
            $_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.1';
            $this->assertEquals($_SERVER['SERVER_PROTOCOL'], $this->Request->getProtocol());
            $this->assertAttributeEquals($_SERVER['SERVER_PROTOCOL'], 'protocol', $this->Request);
        }

        /**
         * Test the supported methods routines.
         * @covers Brickoo\Http\Request::setSupportedMethods
         * @covers Brickoo\Http\Request::getSupportedMethods
         * @covers Brickoo\Http\Request::isSupportedMethod
         */
        public function testSupportedMethods()
        {
            $this->assertSame($this->Request, $this->Request->setSupportedMethods(array('GET')));
            $this->assertAttributeEquals(array('GET'), 'supportedMethods', $this->Request);
            $this->assertEquals(array('GET'), $this->Request->getSupportedMethods());
            $this->assertTrue($this->Request->isSupportedMethod('get'));
            $this->assertFalse($this->Request->isSupportedMethod('post'));
        }

        /**
         * Test the getter and setter of the method.
         * @covers Brickoo\Http\Request::setMethod
         * @covers Brickoo\Http\Request::getMethod
         */
        public function testGetSetMethod()
        {
            $this->assertSame($this->Request, $this->Request->setMethod('POST'));
            $this->assertAttributeEquals('POST', 'method', $this->Request);
            $this->assertEquals('POST', $this->Request->getMethod());
        }

        /**
         * Test if trying to use a wrong argument type throws an exception.
         * @covers Brickoo\Http\Request::setMethod
         * @expectedException InvalidArgumentException
         */
        public function testSetMethodArgumentException()
        {
            $this->Request->setMethod(array('wrongType'));
        }

        /**
         * Test if trying to set a not supported method throws an exception.
         * @covers Brickoo\Http\Request::setMethod
         * @covers Brickoo\Http\Exceptions\MethodNotSupportedException::__construct
         * @expectedException Brickoo\Http\Exceptions\MethodNotSupportedException
         */
        public function testSetMethodSupportedMethodException()
        {
            $this->Request->setMethod('UNKNOWN');
        }

        /**
         * Test to if the method can be retrieved from the globals.
         * @covers Brickoo\Http\Request::getMethod
         */
        public function testGetMethodFromGlobals()
        {
            $_SERVER['REQUEST_METHOD'] = 'HEAD';
            $this->assertEquals('HEAD', $this->Request->getMethod());
        }

        /**
         * Test the getter and setter of the host.
         * @covers Brickoo\Http\Request::setHost
         * @covers Brickoo\Http\Request::getHost
         */
        public function testGetSetHost()
        {
            $Url = $this->getMock('Brickoo\Http\Component\Url', array('setHost', 'getHost'));
            $Url->expects($this->once())
                ->method('setHost')
                ->with('localhost');
            $Url->expects($this->once())
                ->method('getHost')
                ->will($this->returnValue('localhost'));

            $this->Request->Url($Url);
            $this->assertSame($this->Request, $this->Request->setHost('localhost'));
            $this->assertEquals('localhost', $this->Request->getHost());
        }

        /**
         * Test if trying to use a wrong argument throws an exception.
         * @covers Brickoo\Http\Request::setHost
         * @expectedException InvalidArgumentException
         */
        public function testSetHostArgumentException()
        {
            $this->Request->setHost(array('wrongType'));
        }

        /**
         * Test the getter and setter of the path.
         * @covers Brickoo\Http\Request::setPath
         * @covers Brickoo\Http\Request::getPath
         */
        public function testGetSetPath()
        {
            $Url = $this->getMock('Brickoo\Http\Component\Url', array('setPath', 'getPath'));
            $Url->expects($this->once())
                ->method('setPath')
                ->with('/path/to/somewhere/');
            $Url->expects($this->once())
                ->method('getPath')
                ->will($this->returnValue('/path/to/somewhere'));

            $this->Request->Url($Url);
            $this->assertSame($this->Request, $this->Request->setPath('/path/to/somewhere/'));
            $this->assertEquals('/path/to/somewhere', $this->Request->getPath());
        }

        /**
         * Test if trying to use a wrong argument type throws an exception.
         * @covers Brickoo\Http\Request::setPath
         * @expectedException InvalidArgumentException
         */
        public function testSetPathArgumentException()
        {
            $this->Request->setPath(array('wrongType'));
        }

        /**
         * Test the getter and setter of the format.
         * @covers Brickoo\Http\Request::setFormat
         * @covers Brickoo\Http\Request::getFormat
         */
        public function testGetSetFormat()
        {
            $Url = $this->getMock('Brickoo\Http\Component\Url', array('setFormat', 'getFormat'));
            $Url->expects($this->once())
                ->method('setFormat')
                ->with('xml');
            $Url->expects($this->once())
                ->method('getFormat')
                ->will($this->returnValue('xml'));

            $this->Request->Url($Url);
            $this->assertSame($this->Request, $this->Request->setFormat('xml'));
            $this->assertEquals('xml', $this->Request->getFormat());
        }

        /**
         * Test if trying to use a wrong argument type throws an exception.
         * @covers Brickoo\Http\Request::setFormat
         * @expectedException InvalidArgumentException
         */
        public function testSetFormatArgumentException()
        {
            $this->Request->setFormat(array('wrongType'));
        }

        /**
         * Test if a value fromt the global $_SERVER variables.
         * @covers Brickoo\Http\Request::getServerVar
         */
        public function testGetServerVar()
        {
            $_SERVER['TEST'] = 'VALUE';
            $this->assertEquals('VALUE', $this->Request->getServerVar('TEST'));
            $this->assertEquals('DEFAULT_VALUE', $this->Request->getServerVar('UNSET', 'DEFAULT_VALUE'));
        }

        /**
         * Test if the raw data retruned from php://input is of type string.
         * @covers Brickoo\Http\Request::getRawBody
         */
        public function testGetRawBody()
        {
            $this->assertInternalType('string', $this->Request->getRawBody());
        }

        /**
         * Test if the forwarded header is recognized.
         * @covers Brickoo\Http\Request::isSecureConnection
         */
        public function testIsSecureConnectionForwarded()
        {
            $Headers = $this->getMock('Brickoo\Http\Component\Headers');
            $Headers->expects($this->once())
                    ->method('get')
                    ->with('X-Forwarded-Proto')
                    ->will($this->returnValue('HTTPS'));

            $this->Request->Headers($Headers);
            $this->assertTrue($this->Request->isSecureConnection());
        }

        /**
         * Test if the server https variable is recognized.
         * @covers Brickoo\Http\Request::isSecureConnection
         */
        public function testIsSecureConnectionHttps()
        {
            $_SERVER['HTTPS'] = 'on';

            $Headers = $this->getMock('Brickoo\Http\Component\Headers');
            $Headers->expects($this->once())
                    ->method('get')
                    ->with('X-Forwarded-Proto')
                    ->will($this->returnValue(false));

            $this->Request->Headers($Headers);
            $this->assertTrue($this->Request->isSecureConnection());
            unset($_SERVER['HTTPS']);
        }

        /**
         * Test if the https is not availale.
         * @covers Brickoo\Http\Request::isSecureConnection
         */
        public function testIsSecureConnection()
        {
            unset($_SERVER['HTTPS']);

            $Headers = $this->getMock('Brickoo\Http\Component\Headers');
            $Headers->expects($this->once())
                    ->method('get')
                    ->with('X-Forwarded-Proto')
                    ->will($this->returnValue(false));

            $this->Request->Headers($Headers);
            $this->assertFalse($this->Request->isSecureConnection());
        }

        /**
         * Test if the is availale and recognized.
         * @covers Brickoo\Http\Request::isAjaxRequest
         */
        public function testisAjaxRequest()
        {
            $_SERVER['X-Requested-With'] = 'XMLHttpRequest';
            $this->assertTrue($this->Request->isAjaxRequest());
            unset($_SERVER['X-Requested-With']);
        }

        /**
         * Test if the request can be returned as string.
         * @covers Brickoo\Http\Request::toString
         */
        public function testToString()
        {
            $request = "GET /path/to/somewhere HTTP/1.1\r\n";
            $headers = "Test: some test header value\r\n";

            $Headers = $this->getMock('Brickoo\Http\Component\Headers',array('toString'));
            $Headers->expects($this->once())
                    ->method('toString')
                    ->will($this->returnValue($headers));

            $Url = $this->getMock('Brickoo\Http\Component\Url', array('toString'));
            $Url->expects($this->once())
                ->method('toString')
                ->with(false)
                ->will($this->returnValue('/path/to/somewhere'));

            $this->Request->Url($Url)->Headers($Headers);
            $this->Request->setMethod('get')->setHost('localhost')->setProtocol('HTTP/1.1');

            $this->assertEquals($request . $headers, $this->Request->toString());
        }

        /**
         * Test if the request can be returned as string by the magic function __toString.
         * @covers Brickoo\Http\Request::__toString
         */
        public function testMagicToString()
        {
            $request = "GET /path/to/somewhere HTTP/1.1\r\n";
            $headers = "Test: some test header value\r\n";

            $Headers = $this->getMock('Brickoo\Http\Component\Headers',array('toString'));
            $Headers->expects($this->once())
                    ->method('toString')
                    ->will($this->returnValue($headers));

            $Url = $this->getMock('Brickoo\Http\Component\Url', array('toString'));
            $Url->expects($this->once())
                ->method('toString')
                ->with(false)
                ->will($this->returnValue('/path/to/somewhere'));

            $this->Request->Url($Url)->Headers($Headers);
            $this->Request->setMethod('get')->setHost('localhost')->setProtocol('HTTP/1.1');

            $this->assertEquals($request . $headers, (string)$this->Request);
        }

    }