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

namespace Brickoo\Tests\Memory;

use Brickoo\Memory\Container,
    PHPUnit_Framework_TestCase;

/**
 * containerTest
 *
 * Test suite for the container class.
 * @see Brickoo\Memory\Container
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class ContainerTest extends PHPUnit_Framework_TestCase {

    /**
     * Holds an instance of the container class.
     * @var \Brickoo\Memory\Container
     */
    protected $container;

    /**
     * Sets up the container instance used.
     * @return void
     */
    protected function setUp() {
        $this->container = new Container();
    }

    /**
     * @covers Brickoo\Memory\Container::__construct
     * @covers Brickoo\Memory\Container::offsetSet
     * @covers Brickoo\Memory\Container::offsetGet
     * @covers Brickoo\Memory\Container::offsetExists
     * @covers Brickoo\Memory\Container::offsetUnset
     */
    public function testArrayAccess() {
        $this->container["unit"] = "test";
        $this->assertAttributeEquals(array("unit" => "test"), "container", $this->container);
        $this->assertEquals("test", $this->container["unit"]);
        $this->assertNull($this->container["undefined"]);
        $this->assertTrue(isset($this->container["unit"]));
        unset($this->container["unit"]);
        $this->assertAttributeEquals(array(), "container", $this->container);
    }

    /** @covers Brickoo\Memory\Container::getIterator */
    public function testGetInterator() {
        $this->assertInstanceOf("\Iterator", $this->container->getIterator());

    }

    /** @covers Brickoo\Memory\Container::count */
    public function testCountContainerEntries() {
        $this->container->merge(array("key1" => "value1", "key2" => "value2", "key3" => "value3"));
        $this->assertEquals(3, count($this->container));
    }

    /** @covers Brickoo\Memory\Container::get */
    public function testGetContainerEntry() {
        $this->container["unit"] = "test";
        $this->assertEquals("test", $this->container->get("unit"));
        $this->assertEquals("DEFAULT", $this->container->get("undefined", "DEFAULT"));
    }

    /** @covers Brickoo\Memory\Container::set */
    public function testSetContainerEntry() {
        $this->assertSame($this->container, $this->container->set("unit", "test"));
        $this->assertAttributeEquals(array("unit" => "test"), "container", $this->container);
    }

    /** @covers Brickoo\Memory\Container::has */
    public function testHasContainerEntry() {
        $this->container["unit"] = "test";
        $this->assertTrue($this->container->has("unit"));
        $this->assertFalse($this->container->has("undefinied"));
    }

    /** @covers Brickoo\Memory\Container::delete */
    public function testDeleteContaierEntry() {
        $this->container["unit"] = "test";
        $this->assertSame($this->container, $this->container->delete("unit"));
        $this->assertAttributeEquals(array(), "container", $this->container);
    }

    /** @covers Brickoo\Memory\Container::merge */
    public function testMergeCurrentContainerEntriesWithOther() {
        $initData        = array("key1" => "value1");
        $mergeData       = array("key2" => "value2");
        $expectedData    = array_merge($initData, $mergeData);

        $this->container["key1"] = "value1";
        $this->assertAttributeEquals($initData, "container", $this->container);
        $this->assertSame($this->container, $this->container->merge($mergeData));
        $this->assertAttributeEquals($expectedData, "container", $this->container);
    }

    /** @covers Brickoo\Memory\Container::fromArray */
    public function testFromArrayImport() {
        $expected  = array("test", "import");
        $this->assertSame($this->container, $this->container->fromArray($expected));
        $this->assertAttributeEquals($expected, "container", $this->container);
    }

    /** @covers Brickoo\Memory\Container::toArray */
    public function testToArrayExport() {
        $expected = array("test");
        $this->assertSame($this->container, $this->container->fromArray($expected));
        $this->assertEquals($expected, $this->container->toArray());
    }

    /** @covers Brickoo\Memory\Container::isEmpty */
    public function testIsEmptyContainerEntriesList() {
        $this->assertTrue($this->container->isEmpty());
        $this->container["unit"] = "test";
        $this->assertFalse($this->container->isEmpty());
    }

    /** @covers Brickoo\Memory\Container::flush */
    public function testFlushContainerEntries() {
        $this->container["unit"] = "test";
        $this->container->flush();
        $this->assertAttributeEquals(array(), "container", $this->container);
    }

}