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

use Brickoo\Component\Annotation\Annotation,
    Brickoo\Component\Annotation\AnnotationParser,
    PHPUnit_Framework_TestCase;

/**
 * Test suite for the AnnotationParser class.
 * @see Brickoo\Component\Annotation\AnnotationParser
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class AnnotationParserTest extends PHPUnit_Framework_TestCase {

    /**
     * @covers Brickoo\Component\Annotation\AnnotationParser::__construct
     * @covers Brickoo\Component\Annotation\AnnotationParser::setAnnotationPrefix
     */
    public function testSetAnnotationPrefix() {
        $annotationParser = new AnnotationParser();
        $this->assertSame($annotationParser, $annotationParser->setAnnotationPrefix("@:"));
        $this->assertAttributeEquals("@:", "annotationPrefix", $annotationParser);
    }

    /** @covers Brickoo\Component\Annotation\AnnotationParser::setAnnotationWhitelist */
    public function testSetAnnotationWhitelist() {
        $annotationParser = new AnnotationParser();
        $this->assertSame($annotationParser, $annotationParser->setAnnotationWhitelist(["Cache"]));
        $this->assertAttributeEquals(["Cache"], "annotationWhitelist", $annotationParser);
    }

    /**
     * @covers Brickoo\Component\Annotation\AnnotationParser::parse
     * @covers Brickoo\Component\Annotation\AnnotationParser::getAnnotationsMatches
     * @covers Brickoo\Component\Annotation\AnnotationParser::getAnnotationList
     * @covers Brickoo\Component\Annotation\AnnotationParser::isAnnotationInWhitelist
     * @covers Brickoo\Component\Annotation\AnnotationParser::getAnnotationValues
     * @covers Brickoo\Component\Annotation\AnnotationParser::getParameterValues
     * @covers Brickoo\Component\Annotation\AnnotationParser::attachParameterValues
     * @covers Brickoo\Component\Annotation\AnnotationParser::convertValue
     * @covers Brickoo\Component\Annotation\AnnotationParser::transformScalar
     * @covers Brickoo\Component\Annotation\AnnotationParser::convertAnnotations
     */
    public function testParseAnnotatedDocComment() {
        $docComment = '/**
            * @:Assert (":[\'a\', \'b\', key=\'c\']:" false true)
            * Some comment about the implementation.
            * @:Cache (path = "/temp" lifetime = 30)
            * @param string $someValue
            * @return void
            */';

        $target = Annotation::TARGET_CLASS;
        $targetLocation = "";
        $annotationParser = new AnnotationParser();
        $annotationParser->setAnnotationPrefix("@:");
        $annotationParser->setAnnotationWhitelist(["Cache", "Assert"]);
        $annotations = $annotationParser->parse($target, $targetLocation, $docComment);
        $this->assertInternalType("array", $annotations);
        $this->assertEquals(2, count($annotations));

        $annotation_1 = array_pop($annotations);
        $this->assertEquals($target, $annotation_1->getTarget());
        $this->assertEquals($targetLocation, $annotation_1->getTargetLocation());
        $this->assertEquals("Cache", $annotation_1->getName());
        $this->assertEquals(["path" => "/temp", "lifetime" => 30], $annotation_1->getValues());

        $annotation_2 = array_pop($annotations);
        $this->assertEquals($target, $annotation_1->getTarget());
        $this->assertEquals($targetLocation, $annotation_1->getTargetLocation());
        $this->assertEquals("Assert", $annotation_2->getName());
        $this->assertEquals([0 => ['a', 'b', 'key' => 'c'], 1 => false, 2 => true], $annotation_2->getValues());
    }

}
