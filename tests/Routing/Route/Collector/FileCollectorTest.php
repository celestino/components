<?php

/*
 * Copyright (c) 2011-2015, Celestino Diaz <celestino.diaz@gmx.de>
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

namespace Brickoo\Tests\Component\Routing\Route\Collector;

use Brickoo\Component\Routing\Route\Collector\FileRouteCollector;
use PHPUnit_Framework_TestCase;

/**
 * FileRouteCollectorTest
 *
 * Test suite for the FileRouteCollector class.
 * @see Brickoo\Component\Routing\Route\Collector\FileRouteCollector
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class FileRouteCollectorTest extends PHPUnit_Framework_TestCase {

    /** @covers Brickoo\Component\Routing\Route\Collector\FileRouteCollector::__construct */
    public function testConstructorImplementedInterface() {
        $routingPath = __DIR__;
        $routingFilename = "routes.php";
        $searchRecursively = true;

        $fileRouteCollector = new FileRouteCollector($routingPath, $routingFilename, $searchRecursively);
        $this->assertInstanceOf("\\Brickoo\\Component\\Routing\\Route\\Collector\\RouteCollector", $fileRouteCollector);
    }

    /**
     * @covers Brickoo\Component\Routing\Route\Collector\FileRouteCollector::__construct
     * @expectedException \InvalidArgumentException
     */
    public function testEmptyRoutePathThrowsArgumentException() {
        new FileRouteCollector("", "routes.php");
    }

    /**
     * @covers Brickoo\Component\Routing\Route\Collector\FileRouteCollector::__construct
     * @expectedException \InvalidArgumentException
     */
    public function testEmptyRouteFilenameThrowsArgumentException() {
        new FileRouteCollector(__DIR__, "");
    }

    /**
     * @covers Brickoo\Component\Routing\Route\Collector\FileRouteCollector::collect
     * @covers Brickoo\Component\Routing\Route\Collector\FileRouteCollector::collectRouteCollectionsFilePaths
     * @covers Brickoo\Component\Routing\Route\Collector\FileRouteCollector::getFilePaths
     */
    public function testCollectNonRecursively() {
        $routingPath = realpath(dirname(__FILE__)) . DIRECTORY_SEPARATOR."Assets".DIRECTORY_SEPARATOR."Routes";
        $routingFilename = "routes.php";
        $searchRecursively = false;

        $fileRouteCollector = new FileRouteCollector($routingPath, $routingFilename, $searchRecursively);
        $this->assertInstanceOf("\\ArrayIterator", $fileRouteCollector->collect());
        $this->assertEquals(1, $fileRouteCollector->getIterator()->count());
    }

    /**
     * @covers Brickoo\Component\Routing\Route\Collector\FileRouteCollector::collect
     * @covers Brickoo\Component\Routing\Route\Collector\FileRouteCollector::collectRouteCollectionsFilePaths
     * @covers Brickoo\Component\Routing\Route\Collector\FileRouteCollector::getRecursiveFilePaths
     */
    public function testCollectRecursively() {
        $routingPath = realpath(dirname(__FILE__)) . DIRECTORY_SEPARATOR."Assets";
        $routingFilename = "routes.php";
        $searchRecursively = true;

        $fileRouteCollector = new FileRouteCollector($routingPath, $routingFilename, $searchRecursively);
        $this->assertInstanceOf("\\ArrayIterator", $fileRouteCollector->collect());
        $this->assertEquals(2, $fileRouteCollector->getIterator()->count());
    }

    /** covers Brickoo\Component\Routing\Route\Collector\FileRouteCollector::getIterator */
    public function testGetCollectionsIterator() {
        $routingPath = realpath(dirname(__FILE__));
        $routingFilename = "nothing_available.php";
        $searchRecursively = false;

        $fileRouteCollector = new FileRouteCollector($routingPath, $routingFilename, $searchRecursively);
        $this->assertInstanceOf("\\ArrayIterator", $fileRouteCollector->getIterator());
    }

}
