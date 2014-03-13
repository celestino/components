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

use Brickoo\Component\Annotation\AnnotationTarget,
    Brickoo\Component\Annotation\AnnotationTargetTypes,
    PHPUnit_Framework_TestCase;

/**
 * Test suite for the AnnotationTarget class.
 * @see Brickoo\Component\Annotation\AnnotationTarget
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class AnnotationTargetTest extends PHPUnit_Framework_TestCase {

    /**
     * @covers Brickoo\Component\Annotation\AnnotationTarget::__construct
     * @covers Brickoo\Component\Annotation\AnnotationTarget::getType
     * @covers Brickoo\Component\Annotation\AnnotationTarget::isTypeOf
     */
    public function testGetType() {
        $annotationTarget = new AnnotationTarget(AnnotationTargetTypes::TYPE_CLASS, "ClassName", "TargetName");
        $this->assertEquals(AnnotationTargetTypes::TYPE_CLASS, $annotationTarget->getType());
        $this->assertTrue($annotationTarget->isTypeOf(AnnotationTargetTypes:: TYPE_CLASS));
        $this->assertFalse($annotationTarget->isTypeOf(AnnotationTargetTypes:: TYPE_METHOD));
    }

    /** @covers Brickoo\Component\Annotation\AnnotationTarget::getClassName */
    public function testGetClassName() {
        $annotationTarget = new AnnotationTarget(AnnotationTargetTypes::TYPE_CLASS, "ClassName", "TargetName");
        $this->assertEquals("ClassName", $annotationTarget->getClassName());
    }

    /** @covers Brickoo\Component\Annotation\AnnotationTarget::getName */
    public function testGetName() {
        $annotationTarget = new AnnotationTarget(AnnotationTargetTypes::TYPE_CLASS, "ClassName", "TargetName");
        $this->assertEquals("TargetName", $annotationTarget->getName());
    }

}