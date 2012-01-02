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

    use Brickoo\Library\Http\Url;

    // require PHPUnit Autoloader
    require_once ('PHPUnit/Autoload.php');

    /**
     * UrlTest
     *
     * Test suite for the Http Url class.
     * @see Brickoo\Library\Http\Url
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class UrlTest extends PHPUnit_Framework_TestCase
    {

        /**
         * Returns an Core Request Stub.
         * @param array $requestMethods the request methods to attach
         * @return object implementing the Brickoo\Library\Core\Interfaces\RequestInterface
         */
        protected function getCoreRequestStub(array $requestMethods = null)
        {
            return $this->getMock
            (
                'Brickoo\Library\Core\Request',
                ($requestMethods === null ? null : array_values($requestMethods))
            );
        }

        /**
        * Returns an Http Request Stub.
        * @param array $requestMethods the request methods to attach
        * @return object implementing the Brickoo\Library\Http\Interfaces\RequestInterface
        */
        protected function getHttpRequestStub(array $requestMethods = null)
        {
            $HttpRequestStub = $this->getMock
            (
                'Brickoo\Library\Http\Request',
                ($requestMethods === null ? null : array_keys($requestMethods))
            );

            if ($requestMethods !== null)
            {
                foreach($requestMethods as $method => $returnValue)
                {
                    $HttpRequestStub->expects($this->any())
                    ->method($method)
                    ->will($this->returnValue($returnValue));
                }
            }

            return $HttpRequestStub;
        }

        /**
         * Holds an instance of the Url class.
         * @var Brickoo\Library\Http\Url
         */
        protected $Url;

        /**
         * Set up the environment variables used.
         * @return void
         */
        public function setUp()
        {
            $_GET['some'] = 'value';

            $this->Url = new Url();
        }

        /**
         * Test if the class can be created.
         * @covers Brickoo\Library\Http\Url::__construct
         * @covers Brickoo\Library\Http\Url::clear
         * @covers Brickoo\Library\Http\Interfaces\UrlInterface
         */
        public function testUrlConstructor()
        {
            $this->assertInstanceOf
            (
                '\Brickoo\Library\Http\Interfaces\UrlInterface',
                $this->Url
            );
        }

        /**
         * Test if the Http\Request dependency can be retrieved.
         * @covers Brickoo\Library\Http\Url::getRequest
         * @covers Brickoo\Library\Http\Url::injectRequest
         */
        public function testGetRequest()
        {
            $this->assertInstanceOf
            (
                '\Brickoo\Library\Http\Interfaces\RequestInterface',
                $this->Url->getRequest()
            );
        }

        /**
         * Test if the Http\Request can be injected.
         * @covers Brickoo\Library\Http\Url::injectRequest
         */
        public function testInjectRequest()
        {
            $HttpRequestStub = $this->getHttpRequestStub();
            $this->Url->injectRequest($HttpRequestStub);
            $this->assertSame($HttpRequestStub, $this->Url->getRequest());
        }

        /**
         * Test if trying to overwrite the Http\Request dependecy throws an exception.
         * @covers Brickoo\Library\Http\Url::injectRequest
         * @covers Brickoo\Library\Core\Exceptions\DependencyOverwriteException
         * @expectedException Brickoo\Library\Core\Exceptions\DependencyOverwriteException
         */
        public function testInjectRequestDependencyException()
        {
            $HttpRequest = $this->Url->getRequest();
            $this->Url->injectRequest($this->getHttpRequestStub());
        }

        /**
         * Test if the scheme can be recogi`nized as http.
         * @covers Brickoo\Library\Http\Url::getScheme
         */
        public function testGetScheme()
        {
            $HttpRequestStub = $this->getHttpRequestStub(array('isSecureConnection' => false));
            $this->Url->injectRequest($HttpRequestStub);

            $this->assertEquals('http', $this->Url->getScheme());
        }

        /**
         * Test if the scheme can be recognied as https.
         * @covers Brickoo\Library\Http\Url::getScheme
         */
        public function testGetSecureScheme()
        {
            $HttpRequestStub = $this->getHttpRequestStub(array('isSecureConnection' => true));
            $this->Url->injectRequest($HttpRequestStub);

            $this->assertEquals('https', $this->Url->getScheme());
        }

        /**
         * Test if the host can be recognied by HTTP1.1 header.
         * @covers Brickoo\Library\Http\Url::getHost
         */
        public function testGetHost()
        {
            $HttpRequestStub = $this->getHttpRequestStub(array('getHTTPHeader' => 'testdomain.net'));
            $this->Url->injectRequest($HttpRequestStub);

            $this->assertEquals('testdomain.net', $this->Url->getHost());
        }

        /**
         * Test if the server name as host can be recognied.
         * @covers Brickoo\Library\Http\Url::getHost
         */
        public function testGetHostByServerName()
        {
            $RequestStub = $this->getCoreRequestStub(array('getServerVar'));
            $RequestStub->expects($this->once())
                        ->method('getServerVar')
                        ->will($this->returnValue('myServerName'));
            $HttpRequestStub = $this->getHttpRequestStub(array('getHTTPHeader' => false));
            $HttpRequestStub->injectCoreRequest($RequestStub);

            $this->Url->injectRequest($HttpRequestStub);

            $this->assertEquals('myServerName', $this->Url->getHost());
        }

        /**
         * Test if the server adress as host can be recognied.
         * @covers Brickoo\Library\Http\Url::getHost
         */
        public function testGetHostByServerAdress()
        {
            $RequestStub = $this->getCoreRequestStub(array('getServerVar'));
            $RequestStub->expects($this->exactly(2))
                        ->method('getServerVar')
                        ->will($this->onConsecutiveCalls(null, '123.456.789.000'));
            $HttpRequestStub = $this->getHttpRequestStub(array('getHTTPHeader' => false));
            $HttpRequestStub->injectCoreRequest($RequestStub);

            $this->Url->injectRequest($HttpRequestStub);

            $this->assertEquals('123.456.789.000', $this->Url->getHost());
        }

        /**
         * Test if the port is recognized and can be retrieved.
         * @covers Brickoo\Library\Http\Url::getPort
         */
        public function testGetPort()
        {
            $RequestStub = $this->getCoreRequestStub(array('getServerVar'));
            $RequestStub->expects($this->exactly(2))
                        ->method('getServerVar')
                        ->will($this->onConsecutiveCalls(null, '8080'));
            $HttpRequestStub = $this->getHttpRequestStub();
            $HttpRequestStub->injectCoreRequest($RequestStub);

            $this->Url->injectRequest($HttpRequestStub);

            $this->assertEquals('8080', $this->Url->getPort());
        }

        /**
         * Test if the request query is returned.
         * @covers Brickoo\Library\Http\Url::getRequestQuery
         */
        public function testGetRequestQuery()
        {
            $RequestStub = $this->getCoreRequestStub(array('getServerVar'));
            $RequestStub->expects($this->once())
                        ->method('getServerVar')
                        ->will($this->returnValue('some=value'));
            $HttpRequestStub = $this->getHttpRequestStub();
            $HttpRequestStub->injectCoreRequest($RequestStub);

            $this->Url->injectRequest($HttpRequestStub);

            $this->assertEquals('some=value', $this->Url->getRequestQuery());
        }

        /**
         * Test if the request query from the superglobal _GET is returned.
         * @covers Brickoo\Library\Http\Url::getRequestQuery
         */
        public function testGetRequestQueryFromGet()
        {
            $RequestStub = $this->getCoreRequestStub(array('getServerVar'));
            $RequestStub->expects($this->once())
                        ->method('getServerVar')
                        ->will($this->returnValue(null));
            $HttpRequestStub = $this->getHttpRequestStub();
            $HttpRequestStub->injectCoreRequest($RequestStub);

            $this->Url->injectRequest($HttpRequestStub);

            $this->assertEquals('some=value', $this->Url->getRequestQuery());
        }

        /**
         * Test if the request query without content is returned.
         * @covers Brickoo\Library\Http\Url::getRequestQuery
         */
        public function testGetRequestQueryEmpty()
        {
            $_GET = array();
            $RequestStub = $this->getCoreRequestStub(array('getServerVar'));
            $RequestStub->expects($this->once())
                        ->method('getServerVar')
                        ->will($this->returnValue(null));
            $HttpRequestStub = $this->getHttpRequestStub();
            $HttpRequestStub->injectCoreRequest($RequestStub);

            $this->Url->injectRequest($HttpRequestStub);

            $this->assertInternalType('string', $this->Url->getRequestQuery());
            $this->assertEquals('', $this->Url->getRequestQuery());
        }

        /**
         * Test if the request path is returned.
         * @covers Brickoo\Library\Http\Url::getRequestPath
         * @covers Brickoo\Library\Http\Url::getIISRequestPath
         */
        public function testGetRequestPath()
        {
            $RequestStub = $this->getCoreRequestStub(array('getServerVar'));
            $RequestStub->expects($this->exactly(3))
                        ->method('getServerVar')
                        ->will($this->onConsecutiveCalls(null, null, '/path/to/location.html?some=value'));
            $HttpRequestStub = $this->getHttpRequestStub();
            $HttpRequestStub->injectCoreRequest($RequestStub);

            $this->Url->injectRequest($HttpRequestStub);

            $this->assertEquals('/path/to/location.html', $this->Url->getRequestPath());
            $this->assertEquals('/path/to/location.html', $this->Url->getRequestPath());
        }

        /**
         * Test if the ISS original request path is returned.
         * @covers Brickoo\Library\Http\Url::getRequestPath
         * @covers Brickoo\Library\Http\Url::getIISRequestPath
         */
        public function testGetIISRequestPathByOriginalUrl()
        {
            $RequestStub = $this->getCoreRequestStub(array('getServerVar'));
            $RequestStub->expects($this->once())
                        ->method('getServerVar')
                        ->will($this->returnValue('/path/to/location.html'));
            $HttpRequestStub = $this->getHttpRequestStub();
            $HttpRequestStub->injectCoreRequest($RequestStub);

            $this->Url->injectRequest($HttpRequestStub);

            $this->assertEquals('/path/to/location.html', $this->Url->getRequestPath());
        }

        /**
         * Test if the ISS rewrite request path is returned.
         * @covers Brickoo\Library\Http\Url::getRequestPath
         * @covers Brickoo\Library\Http\Url::getIISRequestPath
         */
        public function testGetIISRequestPathByRewriteUrl()
        {
            $RequestStub = $this->getCoreRequestStub(array('getServerVar'));
            $RequestStub->expects($this->exactly(2))
                        ->method('getServerVar')
                        ->will($this->onConsecutiveCalls(null, '/path/to/location.html'));
            $HttpRequestStub = $this->getHttpRequestStub();
            $HttpRequestStub->injectCoreRequest($RequestStub);

            $this->Url->injectRequest($HttpRequestStub);

            $this->assertEquals('/path/to/location.html', $this->Url->getRequestPath());
        }

        /**
         * Test if the request path segments are returned.
         * @covers Brickoo\Library\Http\Url::getSegments
         */
        public function testGetSegments()
        {
            $RequestStub = $this->getCoreRequestStub(array('getServerVar'));
            $RequestStub->expects($this->once())
                        ->method('getServerVar')
                        ->with('X.Original.Url')
                        ->will($this->returnValue('path/to/location.html'));
            $HttpRequestStub = $this->getHttpRequestStub();
            $HttpRequestStub->injectCoreRequest($RequestStub);

            $this->Url->injectRequest($HttpRequestStub);

            $segments = $this->Url->getSegments();
            $this->assertInternalType('array', $segments);
            $this->assertEquals(array('path', 'to', 'location.html'), $segments);
        }

        /**
         * Test if the request path segment value is returned.
         * @covers Brickoo\Library\Http\Url::getSegment
         */
        public function testGetSegment()
        {
            $RequestStub = $this->getCoreRequestStub(array('getServerVar'));
            $RequestStub->expects($this->once())
                        ->method('getServerVar')
                        ->with('X.Original.Url')
                        ->will($this->returnValue('path/to/location.html'));
            $HttpRequestStub = $this->getHttpRequestStub();
            $HttpRequestStub->injectCoreRequest($RequestStub);

            $this->Url->injectRequest($HttpRequestStub);

            $this->assertContains('path', $this->Url->getSegment(0));
        }

        /**
         * Test if the unavailable segment index throws an exception.
         * @covers Brickoo\Library\Http\Url::getSegment
         * @expectedException OutOfRangeException
         */
        public function testGetSegmentException()
        {
            $RequestStub = $this->getCoreRequestStub(array('getServerVar'));
            $RequestStub->expects($this->once())
                        ->method('getServerVar')
                        ->with('X.Original.Url')
                        ->will($this->returnValue('path/to/location.html'));
            $HttpRequestStub = $this->getHttpRequestStub();
            $HttpRequestStub->injectCoreRequest($RequestStub);

            $this->Url->injectRequest($HttpRequestStub);

            $this->Url->getSegment(9);
        }

        /**
         * Test if the invalid argument throws an exception.
         * @covers Brickoo\Library\Http\Url::getSegment
         * @expectedException InvalidArgumentException
         */
        public function testGetSegmentArgumentException()
        {
            $this->Url->getSegment('0');
        }

        /**
         * Test if the count interface method returns the number of segments.
         * @covers Brickoo\Library\Http\Url::count
         */
        public function testCount()
        {
            $RequestStub = $this->getCoreRequestStub(array('getServerVar'));
            $RequestStub->expects($this->once())
                        ->method('getServerVar')
                        ->with('X.Original.Url')
                        ->will($this->returnValue('path/to/location.html'));
            $HttpRequestStub = $this->getHttpRequestStub();
            $HttpRequestStub->injectCoreRequest($RequestStub);

            $this->Url->injectRequest($HttpRequestStub);

            $this->assertEquals(3, count($this->Url));
        }

        /**
         * Test if the current URL is returned.
         * @covers Brickoo\Library\Http\Url::getRequestUrl
         */
        public function testGetRequestUrl()
        {
            $RequestStub = $this->getCoreRequestStub(array('getServerVar'));
            $RequestStub->expects($this->exactly(3))
                        ->method('getServerVar')
                        ->will($this->onConsecutiveCalls('80', 'some=value', 'path/to/location.html'));
            $HttpRequestStub = $this->getHttpRequestStub(array('getHTTPHeader' => 'testdomain.net', 'isSecureConnection' => true));
            $HttpRequestStub->injectCoreRequest($RequestStub);

            $this->Url->injectRequest($HttpRequestStub);

            $this->assertEquals
            (
                'https://testdomain.net:80/path/to/location.html?some=value',
                $this->Url->getRequestUrl(true)
            );
        }

    }

?>