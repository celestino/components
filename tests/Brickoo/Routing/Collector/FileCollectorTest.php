<?php

    /*
     * Copyright (c) 2011-2013, Celestino Diaz <celestino.diaz@gmx.de>.
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

    namespace Tests\Brickoo\Routing\Collector;

    use Brickoo\Routing\Collector\FileCollector;

    /**
     * FileCollectorTest
     *
     * Test suite for the FileCollector class.
     * @see Brickoo\Routing\Collector\FileCollector
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class FileCollectorTest extends \PHPUnit_Framework_TestCase {

        /**
         * @covers Brickoo\Routing\Collector\FileCollector::__construct
         */
        public function testConstructor() {
            $routingPath = __DIR__;
            $routingFilename = "routes.php";
            $searchRecursively = true;

            $FileCollector = new FileCollector($routingPath, $routingFilename, $searchRecursively);
            $this->assertInstanceOf('Brickoo\Routing\Collector\Interfaces\Collector',$FileCollector);
            $this->assertAttributeEquals($routingPath, "routingPath", $FileCollector);
            $this->assertAttributeEquals($routingFilename, "routingFilename", $FileCollector);
            $this->assertAttributeEquals($searchRecursively, "searchRecursively", $FileCollector);
            $this->assertAttributeEquals(array(), "collections", $FileCollector);
        }

        /**
         * @covers Brickoo\Routing\Collector\FileCollector::__construct
         * @expectedException InvalidArgumentException
         */
        public function testEmptyRoutePathThrowsArgumentException() {
            $FileCollector = new FileCollector("", "routes.php");
        }

        /**
         * @covers Brickoo\Routing\Collector\FileCollector::__construct
         * @expectedException InvalidArgumentException
         */
        public function testEmptyRouteFilenameThrowsArgumentException() {
            $FileCollector = new FileCollector(__DIR__, "");
        }

        /**
         * @covers Brickoo\Routing\Collector\FileCollector::collect
         * @covers Brickoo\Routing\Collector\FileCollector::collectRouteCollections
         * @covers Brickoo\Routing\Collector\FileCollector::getFilePaths
         */
        public function testCollectInOnDirectory() {
            $routingPath = realpath(dirname(__FILE__)) . DIRECTORY_SEPARATOR ."Assets".DIRECTORY_SEPARATOR ."Routes";
            $routingFilename = "routes.php";
            $searchRecursively = false;

            $FileCollector = new FileCollector($routingPath, $routingFilename, $searchRecursively);
            $this->assertSame($FileCollector, $FileCollector->collect());
            $this->assertAttributeCount(1, "collections", $FileCollector);
        }

        /**
         * @covers Brickoo\Routing\Collector\FileCollector::collect
         * @covers Brickoo\Routing\Collector\FileCollector::collectRouteCollections
         * @covers Brickoo\Routing\Collector\FileCollector::getRecursiveFilePaths
         */
        public function testCollectRecursively() {
            $routingPath = realpath(dirname(__FILE__)) . DIRECTORY_SEPARATOR ."Assets";
            $routingFilename = "routes.php";
            $searchRecursively = true;

            $FileCollector = new FileCollector($routingPath, $routingFilename, $searchRecursively);
            $this->assertSame($FileCollector, $FileCollector->collect());
            $this->assertAttributeCount(2, "collections", $FileCollector);
        }

        /**
         * @covers Brickoo\Routing\Collector\FileCollector::collect
         * @covers Brickoo\Routing\Collector\FileCollector::collectRouteCollections
         * @covers Brickoo\Routing\Collector\Exceptions\RoutesNotAvailable
         * @expectedException Brickoo\Routing\Collector\Exceptions\RoutesNotAvailable
         */
        public function testCollectWithoutRoutesThrowsException() {
            $routingPath = realpath(dirname(__FILE__));
            $routingFilename = "nothing_available.php";
            $searchRecursively = false;

            $FileCollector = new FileCollector($routingPath, $routingFilename, $searchRecursively);
            $FileCollector->collect();
        }

        /**
         * covers Brickoo\Routing\Collector\FileCollector::getIterator
         */
        public function testGetCollectionsIterator() {
            $routingPath = realpath(dirname(__FILE__));
            $routingFilename = "nothing_available.php";
            $searchRecursively = false;

            $FileCollector = new FileCollector($routingPath, $routingFilename, $searchRecursively);
            $this->assertInstanceOf('ArrayIterator', $FileCollector->getIterator());
        }

    }