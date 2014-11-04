<?php

/*
 * Copyright (c) 2011-2014, Celestino Diaz <celestino.diaz@gmx.de>.
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
