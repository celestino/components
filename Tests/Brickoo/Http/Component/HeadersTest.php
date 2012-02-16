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

    use Brickoo\Library\Http\Component\Headers;

    // require PHPUnit Autoloader
    require_once ('PHPUnit/Autoload.php');

    /**
     * Fixture for the apache function.
     * @return array sample header values
     *
     */
    function apache_request_headers()
    {
        return array('Apache-Header' => 'APACHE');
    }

    /**
     * HeadersTest
     *
     * Test suite for the Headers class.
     * @see Brickoo\Library\Component\Headers
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class HeadersTest extends \PHPUnit_Framework_TestCase
    {

        /**
         * Holds an instance if the Headers class.
         * @var \Brickoo\Library\Http\Component\Headers
         */
        protected $Headers;

        /**
         * Set up the Headers instance used.
         * @return void
         */
        protected function setUp()
        {
            $this->Headers = new Headers();
        }

        /**
         * Test if the Headers class implements the HeadersInterface and the properties are initialized.
         * @covers Brickoo\Library\Http\Component\Headers::__construct
         */
        public function testConstruct()
        {
            $this->assertInstanceOf('Brickoo\Library\Http\Component\Headers', $this->Headers);
            $this->assertAttributeInternalType('array', 'acceptTypes', $this->Headers);
            $this->assertAttributeInternalType('array', 'acceptCharsets', $this->Headers);
            $this->assertAttributeInternalType('array', 'acceptLanguages', $this->Headers);
            $this->assertAttributeInternalType('array', 'acceptEncodings', $this->Headers);
        }

        /**
         * Test if the accept types can be retrieved.
         * @covers Brickoo\Library\Http\Component\Headers::getAcceptTypes
         * @covers Brickoo\Library\Http\Component\Headers::getAcceptHeaderByRegex
         */
        public function testGetAcceptTypes()
        {
            $expectedTypes = array
            (
                "*/*"                      => 0.8,
                "application/xml"          => 0.9,
                "application/xhtml+xml"    => 1,
                "text/html"                => 1
            );
            $this->Headers->add('Accept', 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8');
            $this->assertEquals($expectedTypes, $this->Headers->getAcceptTypes());
        }

        /**
         * Test if the types can be recognized as supported.
         * @covers Brickoo\Library\Http\Component\Headers::isTypeSupported
         */
        public function testIsTypeSupported()
        {
            $this->Headers->add('Accept', 'text/html,application/xml;q=0.9,*/*;q=0.8');
            $this->assertTrue($this->Headers->isTypeSupported('application/xml'));
            $this->assertTrue($this->Headers->isTypeSupported('text/html'));
            $this->assertTrue($this->Headers->isTypeSupported('*/*'));
        }

        /**
         * Test is a wrong argument type throws an exception.
         * @covers Brickoo\Library\Http\Component\Headers::isTypeSupported
         * @expectedException InvalidArgumentException
         */
        public function testIsTypeSupportedArgumentException()
        {
            $this->Headers->isTypeSupported(null);
        }

        /**
         * Test if the accept languages can be retrieved.
         * @covers Brickoo\Library\Http\Component\Headers::getAcceptLanguages
         * @covers Brickoo\Library\Http\Component\Headers::getAcceptHeaderByRegex
         */
        public function testGetAcceptLanguages()
        {
            $expectedLanguages = array
            (
                "de-DE" => 1,
                "de"    => 0.8,
                "en-US" => 0.6,
                "en"    => 0.4
            );
            $this->Headers->add('Accept-Language', 'de-DE,de;q=0.8,en-US;q=0.6,en;q=0.4');
            $this->assertEquals($expectedLanguages, $this->Headers->getAcceptLanguages());
        }

        /**
         * Test if the language can be recognized as supported.
         * @covers Brickoo\Library\Http\Component\Headers::isLanguageSupported
         */
        public function testIsLanguageSupported()
        {
            $this->Headers->add('Accept-Language', 'de-DE,de;q=0.8,en-US;q=0.6,en;q=0.4');
            $this->assertTrue($this->Headers->isLanguageSupported('de-DE'));
            $this->assertTrue($this->Headers->isLanguageSupported('de'));
            $this->assertTrue($this->Headers->isLanguageSupported('en-US'));
            $this->assertTrue($this->Headers->isLanguageSupported('en'));
        }

        /**
         * Test is a wrong argument type throws an exception.
         * @covers Brickoo\Library\Http\Component\Headers::isLanguageSupported
         * @expectedException InvalidArgumentException
         */
        public function testIsLanguageSupportedArgumentException()
        {
            $this->Headers->isLanguageSupported(null);
        }

        /**
         * Test if the accept encodings can be retrieved.
         * @covers Brickoo\Library\Http\Component\Headers::getAcceptEncodings
         * @covers Brickoo\Library\Http\Component\Headers::getAcceptHeaderByRegex
         */
        public function testGetAcceptEncodings()
        {
            $expectedEncodings = array
            (
                "gzip"       => 1,
                "deflate"    => 1,
                "special"    => 0.1
            );
            $this->Headers->add('Accept-Encoding', 'gzip,deflate,special;q=0.1');
            $this->assertEquals($expectedEncodings, $this->Headers->getAcceptEncodings());
        }

        /**
         * Test if the encoding can be recognized as supported.
         * @covers Brickoo\Library\Http\Component\Headers::isEncodingSupported
         */
        public function testIsEncodingSupported()
        {
            $this->Headers->add('Accept-Encoding', 'gzip,deflate,special;q=0.1');
            $this->assertTrue($this->Headers->isEncodingSupported('deflate'));
            $this->assertTrue($this->Headers->isEncodingSupported('gzip'));
            $this->assertTrue($this->Headers->isEncodingSupported('special'));
        }

        /**
         * Test is a wrong argument type throws an exception.
         * @covers Brickoo\Library\Http\Component\Headers::isEncodingSupported
         * @expectedException InvalidArgumentException
         */
        public function testIsEncodingSupportedArgumentException()
        {
            $this->Headers->isEncodingSupported(null);
        }

        /**
         * Test if the accept charsets can be retrieved.
         * @covers Brickoo\Library\Http\Component\Headers::getAcceptCharsets
         * @covers Brickoo\Library\Http\Component\Headers::getAcceptHeaderByRegex
         */
        public function testGetAcceptCharsets()
        {
            $expectedCharsets = array
            (
                "ISO-8859-1"   => 1,
                "utf-8"        => 0.7,
                "*"            => 0.3
            );
            $this->Headers->add('Accept-Charset', 'ISO-8859-1,utf-8;q=0.7,*;q=0.3');
            $this->assertEquals($expectedCharsets, $this->Headers->getAcceptCharsets());
        }

        /**
         * Test if the charset can be recognized as supported.
         * @covers Brickoo\Library\Http\Component\Headers::isCharsetSupported
         */
        public function testIsCharsetSupported()
        {
            $this->Headers->add('Accept-Charset', 'ISO-8859-1,utf-8;q=0.7,*;q=0.3');
            $this->assertTrue($this->Headers->isCharsetSupported('ISO-8859-1'));
            $this->assertTrue($this->Headers->isCharsetSupported('utf-8'));
            $this->assertTrue($this->Headers->isCharsetSupported('*'));
        }

        /**
         * Test if passing an wrong type it throws an exception.
         * @covers Brickoo\Library\Http\Component\Headers::isCharsetSupported
         * @expectedException InvalidArgumentException
         */
        public function testIsCharsetSupportedArgumentException()
        {
            $this->Headers->isCharsetSupported(array('wrongType'));
        }

        /**
         * Test if the parameters are validated.
         * @covers Brickoo\Library\Http\Component\Headers::getAcceptHeaderByRegex
         */
        public function testGetAcceptHeaderByRegex()
        {
            $this->Headers->add('Accept-Charset', 'ISO-8859-1;q=0.3');
            $this->assertEquals
            (
                array('ISO-8859-1' => 0.3),
                $this->Headers->getAcceptHeaderByRegex
                (
                    '~^(?<charset>[a-z0-9\-\*]+)\s*(\;\s*q\=(?<quality>(0\.\d{1,5}|1\.0|[01])))?$~i',
                    'charset',
                    $this->Headers->get('Accept-Charset')
                )
            );
        }

        /**
         * Test if the server request headers can be imported.
         * @covers Brickoo\Library\Http\Component\Headers::importFromGlobals
         * @covers Brickoo\Library\Http\Component\Headers::normalizeHeaders
         */
        public function testImportFromGlobals()
        {
            $expectedHeaders = array
            (
                'Unittest'        => 'yes',
                'Apache-Header'   => 'APACHE'
            );

            $_SERVER['HTTP_UNITTEST'] = 'yes';
            $this->assertSame($this->Headers, $this->Headers->importFromGlobals());
            $this->assertAttributeEquals($expectedHeaders, 'container', $this->Headers);
            unset($_SERVER['HTTP_UNITTEST']);
        }

        /**
         * Test if the request headers can be retrieved.
         * @covers Brickoo\Library\Http\Component\Headers::getRequestHeaders
         */
        public function testGetRequestHeaders()
        {
            $expectedHeaders = array
            (
                'UNITTEST'        => 'yes',
                'Apache-Header'   => 'APACHE'
            );
            $_SERVER['HTTP_UNITTEST'] = 'yes';
            $this->assertEquals($expectedHeaders, $this->Headers->getRequestHeaders());
            unset($_SERVER['HTTP_UNITTEST']);
        }

        /**
         * Test if the request headers can be imported from string.
         * @covers Brickoo\Library\Http\Component\Headers::importFromString
         * @covers Brickoo\Library\Http\Component\Headers::normalizeHeaders
         */
        public function testImportFromString()
        {
            $expectedHeaders = array
            (
                "Accept"            => "text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8",
                "Accept-Language"   => "en-us,en;q=0.5",
                "Accept-Encoding"   => "gzip,deflate",
                "Accept-Charset"    => "ISO-8859-1,utf-8;q=0.7,*;q=0.7",
                "Special"           => array('some', 'values')
            );

            $headers = "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8\r\n".
                       "Accept-Language: en-us,en;q=0.5\r\n".
                       "Accept-Encoding: gzip,deflate\r\n".
                       "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7\r\n".
                       "Special: some\r\n".
                       "Special: values\r\n";

            $this->assertSame($this->Headers, $this->Headers->importFromString($headers));
            $this->assertAttributeEquals($expectedHeaders, 'container', $this->Headers);
        }

        /**
         * Test if the headers can be converterd to string.
         * @covers Brickoo\Library\Http\Component\Headers::toString
         * @covers Brickoo\Library\Http\Component\Headers::__toString
         * @covers Brickoo\Library\Http\Component\Headers::normalizeHeaders
         */
        public function testToString()
        {
            $headers = array
            (
                "Accept"            => "text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8",
                "Accept-Language"   => "en-us,en;q=0.5",
                "Accept-Encoding"   => "gzip,deflate",
                "Accept-Charset"    => "ISO-8859-1,utf-8;q=0.7,*;q=0.7",
                "Special"           => array('some', 'values')
            );

            $expectedHeaders = "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8\r\n".
                       "Accept-Language: en-us,en;q=0.5\r\n".
                       "Accept-Encoding: gzip,deflate\r\n".
                       "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7\r\n".
                       "Special: some\r\n".
                       "Special: values\r\n";

            $this->assertSame($this->Headers, $this->Headers->merge($headers));
            $this->assertEquals($expectedHeaders, $this->Headers->toString());
            $this->assertEquals($expectedHeaders, (string)$this->Headers);

        }

    }