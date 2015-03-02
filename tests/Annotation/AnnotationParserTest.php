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

namespace Brickoo\Tests\Component\Annotation;

use Brickoo\Component\Annotation\Annotation;
use Brickoo\Component\Annotation\AnnotationParser;
use PHPUnit_Framework_TestCase;

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
     * @covers Brickoo\Component\Annotation\AnnotationParser::transformIfIsNumeric
     * @covers Brickoo\Component\Annotation\AnnotationParser::transformIfIsBoolean
     * @covers Brickoo\Component\Annotation\AnnotationParser::convertAnnotations
     */
    public function testParseAnnotatedDocComment() {
        $docComment = '/**
            * @:Assert ("[[\'a\', \'b\', key=\'c\']]" false true)
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
