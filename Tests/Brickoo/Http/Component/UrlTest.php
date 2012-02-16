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

    use Brickoo\Http\Component\Url;

    // require PHPUnit Autoloader
    require_once ('PHPUnit/Autoload.php');

    /**
     * UrlTest
     *
     * Test suite for the Url class.
     * @see Brickoo\Component\Url
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class UrlTest extends PHPUnit_Framework_TestCase
    {

        /**
         * Holds an instance if the Url class.
         * @var \Brickoo\Http\Component\Url
         */
        protected $Url;

        /**
         * Set up the Url instance used.
         * @return void
         */
        protected function setUp()
        {
            $this->Url = new Url();
        }

        /**
         * Test if the Request dependency can be injected and the Url reference is returned.
         * @covers Brickoo\Http\Component\Url::Request
         * @covers Brickoo\Http\Component\Url::getDependency
         */
        public function testRequestInjection()
        {
            $Request = $this->getMock('Brickoo\Http\Request');
            $this->assertSame($this->Url, $this->Url->Request($Request));
            $this->assertAttributeContains($Request, 'dependencies', $this->Url);
        }

        /**
         * Test if the Request dependency can be lazy initialized.
         * @covers Brickoo\Http\Component\Url::Request
         * @covers Brickoo\Http\Component\Url::getDependency
         */
        public function testRequestLazyInitialization()
        {
            $this->assertInstanceOf
            (
                'Brickoo\Http\Interfaces\RequestInterface',
                $this->Url->Request()
            );
            $this->assertAttributeContains($this->Url->Request(), 'dependencies', $this->Url);
        }

        /**
         * Test getter and setter for the scheme.
         * @covers Brickoo\Http\Component\Url::setScheme
         * @covers Brickoo\Http\Component\Url::getScheme
         */
        public function testGetSetScheme()
        {
            $this->assertSame($this->Url, $this->Url->setScheme('http'));
            $this->assertAttributeEquals('http', 'scheme', $this->Url);
            $this->assertEquals('http', $this->Url->getScheme());
        }

        /**
         * Test if the scheme is not set throws an exception.
         * @covers Brickoo\Http\Component\Url::getScheme
         * @expectedException UnexpectedValueException
         */
        public function testGetSchemeValueException()
        {
            $this->Url->getScheme();
        }

        /**
         * Test if trying to use a wrong argument type throws an exception.
         * @covers Brickoo\Http\Component\Url::setHost
         * @expectedException InvalidArgumentException
         */
        public function testSetSchemeArgumentException()
        {
            $this->Url->setScheme(array('wrongType'));
        }

        /**
         * Test getter and setter for the host.
         * @covers Brickoo\Http\Component\Url::setHost
         * @covers Brickoo\Http\Component\Url::getHost
         */
        public function testGetSetHost()
        {
            $this->assertSame($this->Url, $this->Url->setHost('localhost'));
            $this->assertAttributeEquals('localhost', 'host', $this->Url);
            $this->assertEquals('localhost', $this->Url->getHost());
        }

        /**
         * Test if the host is not set throws an exception.
         * @covers Brickoo\Http\Component\Url::getHost
         * @expectedException UnexpectedValueException
         */
        public function testGetHostValueException()
        {
            $this->Url->getHost();
        }

        /**
         * Test if trying to use a wrong argument type throws an exception.
         * @covers Brickoo\Http\Component\Url::setHost
         * @expectedException InvalidArgumentException
         */
        public function testSetHostArgumentException()
        {
            $this->Url->setHost(array('wrongType'));
        }

        /**
         * Test getter and setter for the port.
         * @covers Brickoo\Http\Component\Url::setPort
         * @covers Brickoo\Http\Component\Url::getPort
         */
        public function testGetSetPort()
        {
            $this->assertSame($this->Url, $this->Url->setPort('8080'));
            $this->assertAttributeEquals(8080, 'port', $this->Url);
            $this->assertEquals(8080, $this->Url->getPort());
        }

        /**
         * Test if trying to use a wrong argument type throws an exception.
         * @covers Brickoo\Http\Component\Url::setPort
         * @expectedException InvalidArgumentException
         */
        public function testSetPortArgumentException()
        {
            $this->Url->setPort(array('wrongType'));
        }

        /**
         * Test getter and setter for the query.
         * @covers Brickoo\Http\Component\Url::setQuery
         * @covers Brickoo\Http\Component\Url::getQuery
         */
        public function testGetSetQuery()
        {
            $this->assertSame($this->Url, $this->Url->setQuery('test=value'));
            $this->assertAttributeEquals('test=value', 'query', $this->Url);
            $this->assertEquals('test=value', $this->Url->getQuery());
        }

        /**
         * Test if trying to use a wrong argument type throws an exception.
         * @covers Brickoo\Http\Component\Url::setQuery
         * @expectedException InvalidArgumentException
         */
        public function testSetQueryArgumentException()
        {
            $this->Url->setQuery(array('wrongType'));
        }

        /**
         * Test getter and setter for the path.
         * @covers Brickoo\Http\Component\Url::setPath
         * @covers Brickoo\Http\Component\Url::getPath
         */
        public function testGetSetPath()
        {
            $this->assertSame($this->Url, $this->Url->setPath('/path/to/somewhere/'));
            $this->assertAttributeEquals('/path/to/somewhere', 'path', $this->Url);
            $this->assertEquals('/path/to/somewhere', $this->Url->getPath());
        }

        /**
         * Test if the path is not set throws an exception.
         * @covers Brickoo\Http\Component\Url::getPath
         * @expectedException UnexpectedValueException
         */
        public function testGetPathValueException()
        {
            $this->Url->getPath();
        }

        /**
         * Test if trying to use a wrong argument type throws an exception.
         * @covers Brickoo\Http\Component\Url::setPath
         * @expectedException InvalidArgumentException
         */
        public function testSetPathArgumentException()
        {
            $this->Url->setPath(array('wrongType'));
        }

        /**
         * Test getter and setter for the format.
         * @covers Brickoo\Http\Component\Url::setFormat
         * @covers Brickoo\Http\Component\Url::getFormat
         */
        public function testGetSetFormat()
        {
            $this->assertNull($this->Url->getFormat());
            $this->assertSame($this->Url, $this->Url->setFormat('json'));
            $this->assertAttributeEquals('json', 'format', $this->Url);
            $this->assertEquals('json', $this->Url->getFormat());
        }

        /**
         * Test if trying to use a wrong argument type throws an exception.
         * @covers Brickoo\Http\Component\Url::setFormat
         * @expectedException InvalidArgumentException
         */
        public function testSetFormatArgumentException()
        {
            $this->Url->setFormat(array('wrongType'));
        }

        /**
         * Test if an url can be imported from string.
         * @covers Brickoo\Http\Component\Url::importFromString
         */
        public function testImportFromString()
        {
            $url = 'http://username:password@hostname:8080/path/to/somewhere?argument=value#anker';

            $this->assertSame($this->Url, $this->Url->importFromString($url));
            $this->assertAttributeEquals('http', 'scheme', $this->Url);
            $this->assertAttributeEquals('hostname', 'host', $this->Url);
            $this->assertAttributeEquals(8080, 'port', $this->Url);
            $this->assertAttributeEquals('argument=value', 'query', $this->Url);
            $this->assertAttributeEquals('/path/to/somewhere', 'path', $this->Url);
        }

        /**
         * Test if an url with wrong signature throws an exception.
         * @covers Brickoo\Http\Component\Url::importFromString
         * @expectedException InvalidArgumentException
         */
        public function testImportFromStringArgumentException()
        {
            $this->Url->importFromString('http://');
        }

        /**
         * Test if the configuration is imported by the given methods and the Url reference is returned.
         * @covers Brickoo\Http\Component\Url::importFromGlobals
         */
        public function testImportFromGlobals()
        {
            $Request = $this->getMock('Brickoo\Http\Request', array('isSecureConnection'));
            $Request->expects($this->once())
                    ->method('isSecureConnection')
                    ->will($this->returnValue(true));

            $Url = $this->getMock
            (
                'Brickoo\Http\Component\Url',
                array('getRequestHost', 'getRequestPort', 'getRequestQuery', 'getRequestPath')
            );

            $host    = 'localhost';
            $port    = 8080;
            $query   = 'test=value';
            $path    = '/path/to/somewhere/index.xml';

            $Url->expects($this->once())->method('getRequestHost')->will($this->returnValue($host));
            $Url->expects($this->once())->method('getRequestPort')->will($this->returnValue($port));
            $Url->expects($this->once())->method('getRequestQuery')->will($this->returnValue($query));
            $Url->expects($this->once())->method('getRequestPath')->will($this->returnValue($path));

            $this->assertEquals($Url, $Url->Request($Request)->importFromGlobals());
            $this->assertAttributeEquals('https', 'scheme', $Url);
            $this->assertAttributeEquals('localhost', 'host', $Url);
            $this->assertAttributeEquals(8080, 'port', $Url);
            $this->assertAttributeEquals('test=value', 'query', $Url);
            $this->assertAttributeEquals('/path/to/somewhere/index.xml', 'path', $Url);
            $this->assertAttributeEquals('xml', 'format', $Url);
        }

        /**
         * Test if the host can be returned from the server adress.
         * @covers Brickoo\Http\Component\Url::getRequestHost
         */
        public function testGetRequestHostByServerAdress()
        {
            $valueMap = array
            (
                array('SERVER_NAME', null, null),
                array('SERVER_ADDR', null, '127.0.0.1')
            );

            $Headers = $this->getMock('Brickoo\http\Component\Headers', array('get'));
            $Headers->expects($this->once())
                    ->method('get')
                    ->with('Host')
                    ->will($this->returnValue(null));

            $Request = $this->getMock('Brickoo\Http\Request', array('Headers', 'getServerVar'));
            $Request->expects($this->once())
                    ->method('Headers')
                    ->will($this->returnValue($Headers));
            $Request->expects($this->exactly(2))
                    ->method('getServerVar')
                    ->will($this->returnValueMap($valueMap));

            $this->assertEquals('127.0.0.1', $this->Url->Request($Request)->getRequestHost());
        }

        /**
         * Test if the host can be returned from the server name.
         * @covers Brickoo\Http\Component\Url::getRequestHost
         */
        public function testGetRequestHostByServerName()
        {
            $Headers = $this->getMock('Brickoo\http\Component\Headers', array('get'));
            $Headers->expects($this->once())
                    ->method('get')
                    ->with('Host')
                    ->will($this->returnValue(null));

            $Request = $this->getMock('Brickoo\Http\Request', array('Headers', 'getServerVar'));
            $Request->expects($this->once())
                    ->method('Headers')
                    ->will($this->returnValue($Headers));
            $Request->expects($this->once())
                    ->method('getServerVar')
                    ->will($this->returnValue('localhost'));

            $this->assertEquals('localhost', $this->Url->Request($Request)->getRequestHost());
        }

        /**
         * Test if the host can be returned from the host header.
         * @covers Brickoo\Http\Component\Url::getRequestHost
         */
        public function testGetRequestHostByHost()
        {
            $Headers = $this->getMock('Brickoo\http\Component\Headers', array('get'));
            $Headers->expects($this->once())
                    ->method('get')
                    ->with('Host')
                    ->will($this->returnValue('localhost'));

            $Request = $this->getMock('Brickoo\Http\Request', array('Headers'));
            $Request->expects($this->once())
                    ->method('Headers')
                    ->will($this->returnValue($Headers));

            $this->assertEquals('localhost', $this->Url->Request($Request)->getRequestHost());
        }

        /**
         * Test of the port can be retrieved from the server port.
         * @covers Brickoo\Http\Component\Url::getRequestPort
         */
        public function testGetRequestPortByServerPort()
        {
            $Headers = $this->getMock('Brickoo\http\Component\Headers', array('get'));
            $Headers->expects($this->once())
                    ->method('get')
                    ->with('X-Forwarded-Port')
                    ->will($this->returnValue(null));

            $Request = $this->getMock('Brickoo\Http\Request', array('Headers', 'getServerVar'));
            $Request->expects($this->once())
                    ->method('Headers')
                    ->will($this->returnValue($Headers));
            $Request->expects($this->once())
                    ->method('getServerVar')
                    ->with('SERVER_PORT')
                    ->will($this->returnValue('8080'));

            $this->assertEquals(8080, $this->Url->Request($Request)->getRequestPort());
        }

        /**
         * Test of the port can be retrieved from the forwarded port header.
         * @covers Brickoo\Http\Component\Url::getRequestPort
         */
        public function testGetRequestPortByForwardedPort()
        {
            $Headers = $this->getMock('Brickoo\http\Component\Headers', array('get'));
            $Headers->expects($this->once())
                    ->method('get')
                    ->with('X-Forwarded-Port')
                    ->will($this->returnValue('12345'));

            $Request = $this->getMock('Brickoo\Http\Request', array('Headers'));
            $Request->expects($this->once())
                    ->method('Headers')
                    ->will($this->returnValue($Headers));

            $this->assertEquals(12345, $this->Url->Request($Request)->getRequestPort());
        }

        /**
         * Test if the request query can be retrieved from the $_GET global.
         * @covers Brickoo\Http\Component\Url::getRequestQuery
         */
        public function testGetRequestQueryFromGlobal()
        {
            $_GET = array('test' => 'value');
            $Request = $this->getMock('Brickoo\Http\Request', array('getServerVar'));
            $Request->expects($this->once())
                    ->method('getServerVar')
                    ->with('QUERY_STRING')
                    ->will($this->returnValue(null));

            $this->assertEquals('test=value', $this->Url->Request($Request)->getRequestQuery());
            unset($_GET['test']);
        }

        /**
         * Test if the request query can be retrieved from the server query.
         * @covers Brickoo\Http\Component\Url::getRequestQuery
         */
        public function testGetRequestQueryFromServerQuery()
        {
            $Request = $this->getMock('Brickoo\Http\Request', array('getServerVar'));
            $Request->expects($this->once())
                    ->method('getServerVar')
                    ->with('QUERY_STRING')
                    ->will($this->returnValue('test=value'));

            $this->assertEquals('test=value', $this->Url->Request($Request)->getRequestQuery());
        }

        /**
         * Test if the request path can be retrieved from the server uri.
         * @covers Brickoo\Http\Component\Url::getRequestPath
         */
        public function testGetRequestPath()
        {
            $valueMap = array
            (
                array('X-Original-Url', null, null),
                array('X-Rewrite-Url', null, null)
            );

            $Headers = $this->getMock('Brickoo\http\Component\Headers', array('get'));
            $Headers->expects($this->exactly(2))
                    ->method('get')
                    ->will($this->returnValueMap($valueMap));

            $Request = $this->getMock('Brickoo\Http\Request', array('Headers', 'getServerVar'));
            $Request->expects($this->exactly(2))
                    ->method('Headers')
                    ->will($this->returnValue($Headers));
            $Request->expects($this->once())
                    ->method('getServerVar')
                    ->with('REQUEST_URI')
                    ->will($this->returnValue('/path/to/somewhere?test=value'));

            $this->assertEquals('/path/to/somewhere', $this->Url->Request($Request)->getRequestPath());
        }

        /**
         * Test if the request format can be retrieved from the server path.
         * @covers Brickoo\Http\Component\Url::getRequestFormat
         */
        public function testGetRequestFormat()
        {
            $this->Url->setPath('/some/path/to/index.json');
            $this->assertEquals('json', $this->Url->getRequestFormat());
        }

        /**
         * Test if the request path can be retrieved from the original url header.
         * @covers Brickoo\Http\Component\Url::getRequestPath
         * @covers Brickoo\Http\Component\Url::getIISRequestUrl
         */
        public function testGetIISRequestPathFromOriginalUrlHeader()
        {
            $Headers = $this->getMock('Brickoo\http\Component\Headers', array('get'));
            $Headers->expects($this->once())
                    ->method('get')
                    ->with('X-Original-Url')
                    ->will($this->returnValue('/path/to/somewhere?test=value'));

            $Request = $this->getMock('Brickoo\Http\Request', array('Headers', 'getServerVar'));
            $Request->expects($this->once())
                    ->method('Headers')
                    ->will($this->returnValue($Headers));

            $this->assertEquals('/path/to/somewhere', $this->Url->Request($Request)->getRequestPath());
        }

        /**
         * Test if the request path can be retrieved from the rewrited url header.
         * @covers Brickoo\Http\Component\Url::getRequestPath
         * @covers Brickoo\Http\Component\Url::getIISRequestUrl
         */
        public function testGetIISRequestPathFromRewriteUrlHeader()
        {
            $valueMap = array
            (
                array('X-Original-Url', null, null),
                array('X-Rewrite-Url', null, '/path/to/somewhere?test=value')
            );

            $Headers = $this->getMock('Brickoo\http\Component\Headers', array('get'));
            $Headers->expects($this->exactly(2))
                    ->method('get')
                    ->will($this->returnValueMap($valueMap));

            $Request = $this->getMock('Brickoo\Http\Request', array('Headers', 'getServerVar'));
            $Request->expects($this->exactly(2))
                    ->method('Headers')
                    ->will($this->returnValue($Headers));

            $this->assertEquals('/path/to/somewhere', $this->Url->Request($Request)->getRequestPath());
        }

        /**
         * Test if the complete url can be retrieved as string from the properties.
         * @covers Brickoo\Http\Component\Url::toString
         * @covers Brickoo\Http\Component\Url::__toString
         */
        public function testToString()
        {
            $this->Url->setScheme('http')
                      ->setHost('localhost')
                      ->setPort(8080)
                      ->setPath('/path/to/somewhere')
                      ->setQuery('test=value');

            $this->assertEquals('http://localhost:8080/path/to/somewhere?test=value', $this->Url->toString(true));
            $this->assertEquals('http://localhost:8080/path/to/somewhere?test=value', (string)$this->Url);
        }

    }