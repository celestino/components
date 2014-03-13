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

namespace Brickoo\Tests\Component\Annotation;

use Brickoo\Component\Annotation\Definition,
    Brickoo\Component\Annotation\AnnotationTarget,
    PHPUnit_Framework_TestCase;

/**
 * Test suite for the Definition class.
 * @see Brickoo\Component\Annotation\Definition
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class DefinitionTest extends PHPUnit_Framework_TestCase {

    /**
     * @covers Brickoo\Component\Annotation\Definition::__construct
     * @covers Brickoo\Component\Annotation\Definition::addCollection
     */
    public function testAddCollection() {
        $collection = $this->getDefinitionCollectionStub();
        $definition = new Definition();
        $this->assertSame($definition, $definition->addCollection($collection));
    }

    /** @covers Brickoo\Component\Annotation\Definition::hasCollections */
    public function testHasCollections() {
        $collection = $this->getDefinitionCollectionStub();
        $definition = new Definition();
        $this->assertFalse($definition->hasCollections());
        $definition->addCollection($collection);
        $this->assertTrue($definition->hasCollections());
    }

    /** @covers Brickoo\Component\Annotation\Definition::getCollectionsByTargetType */
    public function testGetCollectionsByTargetType() {
        $collection1 = $this->getDefinitionCollectionStub();
        $collection1->expects($this->any())
                    ->method("isTypeOf")
                    ->with(AnnotationTarget::TYPE_CLASS)
                    ->will($this->returnValue(true));

        $collection2 = $this->getDefinitionCollectionStub();
        $collection2->expects($this->any())
                    ->method("isTypeOf")
                    ->with(AnnotationTarget::TYPE_CLASS)
                    ->will($this->returnValue(false));

        $definition = new Definition();
        $definition->addCollection($collection1)
                   ->addCollection($collection2);
        $this->assertInstanceOf(
            "\\ArrayIterator",
            ($iterator = $definition->getCollectionsByTargetType(AnnotationTarget::TYPE_CLASS))
        );
        $this->assertEquals(1, $iterator->count());
        $this->assertSame($collection1, $iterator->current());
    }

    /** @covers Brickoo\Component\Annotation\Definition::getIterator */
    public function testGetIterator() {
        $collection = $this->getDefinitionCollectionStub();
        $definition = new Definition();
        $definition->addCollection($collection);
        $this->assertInstanceOf("\\ArrayIterator", ($iterator = $definition->getIterator()));
        $this->assertEquals(1, $iterator->count());
        $this->assertSame($collection, $iterator->current());
    }

    /**
     * Returns a DefinitionCollection stub.
     * @return \Brickoo\Component\Annotation\DefinitionCollection
     */
    private function getDefinitionCollectionStub() {
        return $this->getMockBuilder("\\Brickoo\\Component\\Annotation\\DefinitionCollection")
            ->disableOriginalConstructor()
            ->getMock();
    }

}