<?php

/*
 * Copyright (c) 2011-2014, Celestino Diaz <celestino.diaz@gmx.de>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace Brickoo\Tests\Component\Http;

use Brickoo\Component\Http\UriFactory;
use PHPUnit_Framework_TestCase;
use ArrayIterator;
use ReflectionClass;

/**
 * UriFactoryTest
 *
 * Test suite for the UriFactory class.
 * @see Brickoo\Component\Http\UriFactory
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class UriFactoryTest extends PHPUnit_Framework_TestCase {

    /**
     * @covers Brickoo\Component\Http\UriFactory::create
     * @covers Brickoo\Component\Http\UriFactory::createAuthority
     * @covers Brickoo\Component\Http\UriFactory::createQuery
     */
    public function testCreateUri() {
        $uriResolver = $this->getMockBuilder("\\Brickoo\\Component\\Http\\Aggregator\\UriAggregator")
            ->disableOriginalConstructor()->getMock();
        $uriResolver->expects($this->any())
                    ->method("getScheme")
                    ->will($this->returnValue("http"));
        $uriResolver->expects($this->any())
                    ->method("getHostname")
                    ->will($this->returnValue("example.org"));
        $uriResolver->expects($this->any())
                    ->method("getPort")
                    ->will($this->returnValue(80));
        $uriResolver->expects($this->any())
                    ->method("getQueryString")
                    ->will($this->returnValue("a=b&c=d"));
        $uriResolver->expects($this->any())
                    ->method("getFragment")
                    ->will($this->returnValue("section_1"));
        $uriResolver->expects($this->any())
                    ->method("getPath")
                    ->will($this->returnValue("/blog/post/1"));

        $uri = (new UriFactory())->create($uriResolver);
        $this->assertInstanceOf("\\Brickoo\\Component\\Http\\Uri", $uri);
    }

}
