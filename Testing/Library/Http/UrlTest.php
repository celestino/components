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

    use Brickoo\Library\Http\Url;

    // require PHPUnit Autoloader
    require_once ('PHPUnit/Autoload.php');

    /**
     * UrlTest
     *
     * Test case for the Http Url class.
     * @see Brickoo\Library\Http\Url
     * @author Celestino Diaz Teran <celestino@users.sourceforge.net>
     * @version $Id$
     */

    class UrlTest extends PHPUnit_Framework_TestCase
    {

        /**
         * Returns an Core Request Stub for the Url to look up for server variables or http calls.
         * To the request Stub you can add also the Http configuration of methods and values to return.
         * @param array $requestMethods the request methods to attach
         * @param array $httpMethods the http request methods to attach and its return values
         * @return object implementing the Brickoo\Library\Core\Interfaces\Request
         */
        protected function getRequestStub(array $requestMethods = null, array $httpMethods = null)
        {
            $RequestStub = $this->getMock
            (
                'Brickoo\Library\Core\Request',
                ($requestMethods === null ? null : array_values($requestMethods))
            );

            $HttpRequestStub = $this->getMock
            (
                'Brickoo\Library\Http\Request',
                ($httpMethods === null ? null : array_keys($httpMethods)),
                array($RequestStub)
            );

            if ($httpMethods !== null)
            {
                foreach($httpMethods as $method => $returnValue)
                {
                    $HttpRequestStub->expects($this->any())
                                    ->method($method)
                                    ->will($this->returnValue($returnValue));
                }
            }

            $RequestStub->injectHttpRequest($HttpRequestStub);

            return $RequestStub;
        }

        /**
         * Set up the environment variables used.
         * @return void
         */
        public function setUp()
        {
            $_GET['some'] = 'value';
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
                new Url($this->getRequestStub())
            );
        }

        /**
         * Test if the scheme can be recogi´nized as http.
         * @covers Brickoo\Library\Http\Url::getScheme
         */
        public function testGetScheme()
        {
            $RequestStub = $this->getRequestStub
            (
                null,
                array('isSecureConnection' => false)
            );
            $Url = new Url($RequestStub);

            $this->assertEquals('http', $Url->getScheme());
        }

        /**
         * Test if the scheme can be recognied as https.
         * @covers Brickoo\Library\Http\Url::getScheme
         */
        public function testGetSecureScheme()
        {
            $RequestStub = $this->getRequestStub
            (
                null,
                array('isSecureConnection' => true)
            );
            $Url = new Url($RequestStub);

            $this->assertEquals('https', $Url->getScheme());
        }

        /**
         * Test if the host can be recognied by HTTP1.1 header.
         * @covers Brickoo\Library\Http\Url::getHost
         */
        public function testGetHost()
        {
            $RequestStub = $this->getRequestStub
            (
                null,
                array('getHTTPHeader' => 'testdomain.net')
            );
            $Url = new Url($RequestStub);

            $this->assertEquals('testdomain.net', $Url->getHost());
        }

        /**
         * Test if the server name as host can be recognied.
         * @covers Brickoo\Library\Http\Url::getHost
         */
        public function testGetHostByServerName()
        {
            $RequestStub = $this->getRequestStub
            (
                array('getServerVar'),
                array('getHTTPHeader' => false)
            );

            $RequestStub->expects($this->once())
                        ->method('getServerVar')
                        ->will($this->returnValue('myServerName'));

            $Url = new Url($RequestStub);

            $this->assertEquals('myServerName', $Url->getHost());
        }

        /**
         * Test if the server adress as host can be recognied.
         * @covers Brickoo\Library\Http\Url::getHost
         */
        public function testGetHostByServerAdress()
        {
            $RequestStub = $this->getRequestStub
            (
                array('getServerVar'),
                array('getHTTPHeader' => false)
            );
            $RequestStub->expects($this->exactly(2))
                        ->method('getServerVar')
                        ->will($this->onConsecutiveCalls(null, '123.456.789.000'));
            $Url = new Url($RequestStub);

            $this->assertEquals('123.456.789.000', $Url->getHost());
        }

        /**
         * Test if the port can be recognied.
         * @covers Brickoo\Library\Http\Url::getPort
         */
        public function testGetPort()
        {
            $RequestStub = $this->getRequestStub(array('getServerVar'));
            $RequestStub->expects($this->exactly(2))
                        ->method('getServerVar')
                        ->will($this->onConsecutiveCalls(null, '8080'));
            $Url = new Url($RequestStub);

            $this->assertEquals('8080', $Url->getPort());
        }

        /**
         * Test if the request query is returned.
         * @covers Brickoo\Library\Http\Url::getRequestQuery
         */
        public function testGetRequestQuery()
        {
            $RequestStub = $this->getRequestStub(array('getServerVar'));
            $RequestStub->expects($this->once())
                        ->method('getServerVar')
                        ->will($this->returnValue('some=value'));
            $Url = new Url($RequestStub);

            $this->assertEquals('some=value', $Url->getRequestQuery());
        }

        /**
         * Test if the request query from the superglobal _GET is returned.
         * @covers Brickoo\Library\Http\Url::getRequestQuery
         */
        public function testGetRequestQueryFromGet()
        {
            $RequestStub = $this->getRequestStub(array('getServerVar'));
            $RequestStub->expects($this->once())
                        ->method('getServerVar')
                        ->will($this->returnValue(null));
            $Url = new Url($RequestStub);

            $this->assertEquals('some=value', $Url->getRequestQuery());
        }

        /**
         * Test if the request query without content is returned.
         * @covers Brickoo\Library\Http\Url::getRequestQuery
         */
        public function testGetRequestQueryEmpty()
        {
            $_GET = array();
            $RequestStub = $this->getRequestStub(array('getServerVar'));
            $RequestStub->expects($this->once())
                        ->method('getServerVar')
                        ->will($this->returnValue(null));
            $Url = new Url($RequestStub);

            $this->assertInternalType('string', $Url->getRequestQuery());
            $this->assertEquals('', $Url->getRequestQuery());
        }

        /**
         * Test if the request path is returned.
         * @covers Brickoo\Library\Http\Url::getRequestPath
         * @covers Brickoo\Library\Http\Url::getIISRequestPath
         */
        public function testGetRequestPath()
        {
            $RequestStub = $this->getRequestStub(array('getServerVar'));
            $RequestStub->expects($this->exactly(3))
                        ->method('getServerVar')
                        ->will($this->onConsecutiveCalls(null, null, 'path/to/location.html?some=value'));
            $Url = new Url($RequestStub);

            $this->assertEquals('path/to/location.html', $Url->getRequestPath());
            $this->assertEquals('path/to/location.html', $Url->getRequestPath());
        }

        /**
         * Test if the ISS original request path is returned.
         * @covers Brickoo\Library\Http\Url::getRequestPath
         * @covers Brickoo\Library\Http\Url::getIISRequestPath
         */
        public function testGetIISRequestPathByOriginalUrl()
        {
            $RequestStub = $this->getRequestStub(array('getServerVar'));
            $RequestStub->expects($this->once())
                        ->method('getServerVar')
                        ->will($this->returnValue('path/to/location.html'));
            $Url = new Url($RequestStub);

            $this->assertEquals('path/to/location.html', $Url->getRequestPath());
        }

        /**
         * Test if the ISS rewrite request path is returned.
         * @covers Brickoo\Library\Http\Url::getRequestPath
         * @covers Brickoo\Library\Http\Url::getIISRequestPath
         */
        public function testGetIISRequestPathByRewriteUrl()
        {
            $RequestStub = $this->getRequestStub(array('getServerVar'));
            $RequestStub->expects($this->exactly(2))
                        ->method('getServerVar')
                        ->will($this->onConsecutiveCalls(null, 'path/to/location.html'));
            $Url = new Url($RequestStub);

            $this->assertEquals('path/to/location.html', $Url->getRequestPath());
        }

        /**
         * Test if the request path segments are returned.
         * @covers Brickoo\Library\Http\Url::getSegments
         */
        public function testGetSegments()
        {
            $RequestStub = $this->getRequestStub(array('getServerVar'));
            $RequestStub->expects($this->once())
                        ->method('getServerVar')
                        ->with('X.Original.Url')
                        ->will($this->returnValue('path/to/location.html'));
            $Url = new Url($RequestStub);

            $this->assertInternalType('array', ($segments = $Url->getSegments()));
            $this->assertContains('path', $segments);
            $this->assertContains('to', $segments);
            $this->assertContains('location.html', $segments);
        }

        /**
         * Test if the request path segment value is returned.
         * @covers Brickoo\Library\Http\Url::getSegment
         */
        public function testGetSegment()
        {
            $RequestStub = $this->getRequestStub(array('getServerVar'));
            $RequestStub->expects($this->once())
                        ->method('getServerVar')
                        ->with('X.Original.Url')
                        ->will($this->returnValue('path/to/location.html'));
            $Url = new Url($RequestStub);

            $this->assertContains('path', $Url->getSegment(0));
        }

        /**
         * Test if the unavailable segment index throws an exception.
         * @covers Brickoo\Library\Http\Url::getSegment
         * @expectedException OutOfRangeException
         */
        public function testGetSegmentException()
        {
            $RequestStub = $this->getRequestStub(array('getServerVar'));
            $RequestStub->expects($this->once())
                        ->method('getServerVar')
                        ->with('X.Original.Url')
                        ->will($this->returnValue('path/to/location.html'));
            $Url = new Url($RequestStub);

            $Url->getSegment(9);
        }

        /**
         * Test if the invalid argument throws an exception.
         * @covers Brickoo\Library\Http\Url::getSegment
         * @expectedException InvalidArgumentException
         */
        public function testGetSegmentArgumentException()
        {
            $RequestStub = $this->getRequestStub();
            $Url = new Url($RequestStub);

            $Url->getSegment('0');
        }

        /**
         * Test if the count interface method returns the number of segments.
         * @covers Brickoo\Library\Http\Url::count
         */
        public function testCount()
        {
            $RequestStub = $this->getRequestStub(array('getServerVar'));
            $RequestStub->expects($this->once())
                        ->method('getServerVar')
                        ->with('X.Original.Url')
                        ->will($this->returnValue('path/to/location.html'));
            $Url = new Url($RequestStub);

            $this->assertEquals(3, count($Url));
        }

        /**
         * Test if the current URL is returned.
         * @covers Brickoo\Library\Http\Url::getRequestUrl
         */
        public function testGetRequestUrl()
        {
            $RequestStub = $this->getRequestStub
            (
                array('getServerVar'),
                array('getHTTPHeader' => 'testdomain.net', 'isSecureConnection' => true)
            );
            $RequestStub->expects($this->exactly(3))
                        ->method('getServerVar')
                        ->will($this->onConsecutiveCalls('80', 'some=value', 'path/to/location.html'));
            $Url = new Url($RequestStub);

            $this->assertEquals
            (
                'https://testdomain.net:80/path/to/location.html?some=value',
                $Url->getRequestUrl(true)
            );
        }

    }

?>