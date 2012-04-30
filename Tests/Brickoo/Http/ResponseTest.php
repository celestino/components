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

    use Brickoo\Http\Response;

    // require PHPUnit Autoloader
    require_once ('PHPUnit/Autoload.php');

    /**
     * ResponseTest
     *
     * Test suite for the Response class.
     * @see Brickoo\Http\Response
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class ResponseTest extends \PHPUnit_Framework_TestCase {
        /**
         * Holds an instance of the Response class.
         * @var \Brickoo\Http\Response
         */
        protected $Response;

        /**
         * Sets up Response instance used.
         * @return void
         */
        protected function setUp() {
            $this->Response = new Response();
        }

        /**
         * Test if the Response implements the Response.
         * @covers Brickoo\Http\Response::__construct
         */
        public function testConstruct() {
            $this->assertInstanceOf('Brickoo\Http\Interfaces\Response', $this->Response);
        }

        /**
         * Test the Template dependency injection and the Response reference is returned.
         * @covers Brickoo\Http\Response::Template
         * @covers Brickoo\Http\Response::getDependency
         */
        public function testTemplate() {
            $TemplateStub = $this->getMock('Brickoo\Template\Interfaces\Template');
            $this->assertSame($this->Response, $this->Response->Template($TemplateStub));
            $this->assertSame($TemplateStub, $this->Response->Template());
            $this->assertAttributeContains($TemplateStub, 'dependencies', $this->Response);
        }

        /**
         * Test if trying to retrieve a not available Template dependency throws an exception.
         * @covers Brickoo\Http\Response::Template
         * @covers Brickoo\Http\Response::getDependency
         * @covers Brickoo\Http\Exceptions\ResponseTemplateNotAvailableException::__construct
         * @expectedException Brickoo\Http\Exceptions\ResponseTemplateNotAvailableException
         */
        public function testTemplateDependencyException() {
            $this->Response->Template();
        }

        /**
         * Test if the avaibility of the Template dependency is recognized.
         * @covers Brickoo\Http\Response::hasTemplate
         */
        public function testHasTemplate() {
            $TemplateStub = $this->getMock('Brickoo\Template\Interfaces\Template');
            $this->assertFalse($this->Response->hasTemplate());
            $this->assertSame($this->Response, $this->Response->Template($TemplateStub));
            $this->assertTrue($this->Response->hasTemplate());
        }

        /**
         * Test if the Headers can be lazy initialized and returned.
         * @covers Brickoo\Http\Response::Headers
         * @covers Brickoo\Http\Response::getDependency
         */
        public function testHeadersLazyInitialization() {
            $this->assertInstanceOf(
                'Brickoo\Http\Component\Interfaces\Headers',
                ($Headers = $this->Response->Headers())
            );

            $this->assertAttributeContains($Headers, 'dependencies', $this->Response);
        }

        /**
         * Test if the Headers can be injected and the Response reference is returned.
         * @covers Brickoo\Http\Response::Headers
         * @covers Brickoo\Http\Response::getDependency
         */
        public function testHeadersInjection() {
            $HeadersStub = $this->getMock('Brickoo\Http\Component\Interfaces\Headers');
            $this->assertSame($this->Response, $this->Response->Headers($HeadersStub));
            $this->assertAttributeContains($HeadersStub, 'dependencies', $this->Response);
        }

        /**
         * Test if the a available header is recognized.
         * @covers Brickoo\Http\Response::hasHeader
         */
        public function testHasHeader() {
            $HeadersStub = $this->getMock('Brickoo\Http\Component\Headers', array('has'));
            $HeadersStub->expects($this->once())
                        ->method('has')
                        ->with('Content-Type')
                        ->will($this->returnValue(true));

            $this->assertTrue($this->Response->Headers($HeadersStub)->hasHeader('Content-Type'));
        }

        /**
         * Test if trying to use a wrong argument type throws an exception.
         * @covers Brickoo\Http\Response::hasHeader
         * @expectedException InvalidArgumentException
         */
        public function testHasHeaderArgumentException() {
            $this->Response->hasHeader(array('wrongType'));
        }

        /**
         * Test if the headers would be sent to the output buffer.
         * @covers Brickoo\Http\Response::sendHeaders
         */
        public function testSendHeaders() {
            $expectedOutput  = "HTTP/1.1 200 OK\r\n";
            $expectedOutput .= "Unit: TEST\r\n";

            $output = '';

            $callback = function ($header) use (&$output) {
                $output .= $header . "\r\n";
            };

            $this->Response->Headers()->merge(array('UNIT' => 'TEST'))->normalizeHeaders();
            $this->Response->setProtocol('HTTP/1.1')->setStatusCode(200);

            $this->assertSame($this->Response, $this->Response->sendHeaders($callback));
            $this->assertEquals($expectedOutput, $output);
        }

        /**
         * Test if the protocol can be set and retrieved.
         * @covers Brickoo\Http\Response::getProtocol
         * @covers Brickoo\Http\Response::setProtocol
         */
        public function testGetSetProtocol() {
            $this->assertSame($this->Response, $this->Response->setProtocol('HTTP/1.0'));
            $this->assertAttributeEquals('HTTP/1.0', 'protocol', $this->Response);
            $this->assertEquals('HTTP/1.0', $this->Response->getProtocol());
        }

        /**
         * Test if trying to use a wrong protocol throws an exception.
         * @covers Brickoo\Http\Response::setProtocol
         * @expectedException InvalidArgumentException
         */
        public function testSetProcolArgumentException() {
            $this->Response->setProtocol('TEST/2.0');
        }

        /**
         * Test if the status code and phrase can be set and retrieved.
         * @covers Brickoo\Http\Response::getStatusCode
         * @covers Brickoo\Http\Response::setStatusCode
         */
        public function testGetSetStatusCode() {
            $this->assertSame($this->Response, $this->Response->setStatusCode(200));
            $this->assertAttributeEquals(200, 'statusCode', $this->Response);
            $this->assertEquals(200, $this->Response->getStatusCode());
        }

        /**
         * Test if trying to use a wrong argument type throws an exception.
         * @covers Brickoo\Http\Response::setStatusCode
         * @expectedException InvalidArgumentException
         */
        public function testSetStatusCodeArgumentException() {
            $this->Response->setStatusCode('wrongType');
        }

        /**
         * Test if the status code assigned can be recognized.
         * @covers Brickoo\Http\Response::hasStatusCode
         */
        public function testHasStatusCode() {
            $this->Response->setStatusCode(404);
            $this->assertAttributeEquals(404, 'statusCode', $this->Response);
            $this->assertTrue($this->Response->hasStatusCode(404));
            $this->assertTrue($this->Response->hasStatusCode(array(100, 200, 404)));
            $this->assertFalse($this->Response->hasStatusCode(200));
        }

        /**
         * Test if the status phrase can be set and retrieved.
         * @covers Brickoo\Http\Response::getStatusPhrase
         * @covers Brickoo\Http\Response::setStatusPhrase
         */
        public function testGetSetStatusPhrase() {
            $this->assertSame($this->Response, $this->Response->setStatusPhrase(600, 'Unit Test'));
            $this->assertAttributeContains('Unit Test', 'statusPhrases', $this->Response);
            $this->assertEquals('Unit Test', $this->Response->getStatusPhrase(600));
        }

        /**
         * Test if the status phrase from status code set.
         * @covers Brickoo\Http\Response::getStatusPhrase
         */
        public function testGetStatusPhrase() {
            $this->Response->setStatusCode(200);
            $this->assertEquals('OK', $this->Response->getStatusPhrase());
        }

        /**
         * Test if the status code is no set trying to retrieve the phrase throws an exception.
         * @covers Brickoo\Http\Response::getStatusPhrase
         * @covers Brickoo\Http\Exceptions\StatusCodeUnknownException::__construct
         * @expectedException Brickoo\Http\Exceptions\StatusCodeUnknownException
         */
        public function testGetStatusPhraseUnknownException() {
            $this->Response->setStatusCode(900);
            $this->Response->getStatusPhrase();
        }

        /**
         * Test if the content can be ste and retrieved.
         * @covers Brickoo\Http\Response::getContent
         * @covers Brickoo\Http\Response::setContent
         */
        public function testGetSetContent() {
            $this->assertSame($this->Response, $this->Response->setContent('some content'));
            $this->assertAttributeEquals('some content', 'content', $this->Response);
            $this->assertEquals('some content', $this->Response->getContent());
        }

        /**
         * Test if the content can be retrieved from the template dependency.
         * @covers Brickoo\Http\Response::getContent
         */
        public function testGetContentFromTemplate() {
            $expectedOutput = 'some content from template';

            $TemplateStub = $this->getMock('Brickoo\Template\Interfaces\Template', array('render'));
            $TemplateStub->expects($this->once())
                         ->method('render')
                         ->will($this->returnValue($expectedOutput));

            $this->assertEquals($expectedOutput, $this->Response->Template($TemplateStub)->getContent());
        }

        /**
         * Test if tring to use a wrong argument typr throws an exception.
         * @covers Brickoo\Http\Response::setContent
         * @expectedException InvalidArgumentException
         */
        public function testSetContentArgumentException() {
            $this->Response->setContent(array('wrongType'));
        }

        /**
         * Test if the content would be sent to the output buffer.
         * @covers Brickoo\Http\Response::sendContent
         */
        public function testSendContent() {
            $expectedOutput = "some content to sent.";
            $this->Response->setContent('some content to sent.');
            ob_start();
            $this->assertSame($this->Response, $this->Response->sendContent());
            $output = ob_get_clean();
            $this->assertEquals($expectedOutput, $output);
        }

        /**
         * Test if the response including headers and content would be sent.
         * @covers Brickoo\Http\Response::send
         */
        public function testSend() {
            $expectedOutput  = "HTTP/1.1 200 OK\r\n";
            $expectedOutput .= "Unit: TEST\r\n";
            $expectedOutput .= "some content sent.";

            $output = '';

            $callback = function ($header) use (&$output) {
                $output .= $header . "\r\n";
            };

            $this->Response->Headers()->merge(array('UNIT' => 'TEST'))->normalizeHeaders();
            $this->Response->setProtocol('HTTP/1.1')
                           ->setStatusCode(200)
                           ->setContent('some content sent.');

            ob_start();
            $this->assertSame($this->Response, $this->Response->send($callback));
            $output .= ob_get_clean();
            $this->assertEquals($expectedOutput, $output);
        }

        /**
         * Test if the response can be converted to string.
         * @covers Brickoo\Http\Response::toString
         * @covers Brickoo\Http\Response::__toString
         */
        public function testToString() {
            $expectedOutput  = "HTTP/1.1 200 OK\r\n";
            $expectedOutput .= "Unit: TEST\r\n\r\n";
            $expectedOutput .= "some content sent.";

            $this->Response->Headers()->merge(array('UNIT' => 'TEST'))->normalizeHeaders();
            $this->Response->setProtocol('HTTP/1.1')
                           ->setStatusCode(200)
                           ->setContent('some content sent.');

            $this->assertEquals($expectedOutput, $this->Response->toString());
            $this->assertEquals($expectedOutput, (string)$this->Response);
        }

    }