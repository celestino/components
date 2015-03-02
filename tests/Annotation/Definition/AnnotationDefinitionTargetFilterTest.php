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

namespace Brickoo\Tests\Component\Annotation\Definition;

use Brickoo\Component\Annotation\Annotation;
use Brickoo\Component\Annotation\Definition\AnnotationDefinition;
use Brickoo\Component\Annotation\Definition\AnnotationDefinitionTargetFilter;
use Brickoo\Component\Common\Collection;
use PHPUnit_Framework_TestCase;

/**
 * Test suite for the AnnotationDefinitionTargetFilter class.
 * @see Brickoo\Component\Annotation\Definition\AnnotationDefinitionTargetFilter
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class AnnotationDefinitionTargetFilterTest extends PHPUnit_Framework_TestCase {

    /**
     * @covers Brickoo\Component\Annotation\Definition\AnnotationDefinitionTargetFilter::__construct
     * @covers Brickoo\Component\Common\Exception\InvalidTypeException
     * @expectedException \Brickoo\Component\Common\Exception\InvalidTypeException
     */
    public function testCollectionItemsMustBeOfTypeAnnotationDefinition() {
        new AnnotationDefinitionTargetFilter(new Collection(["wrongType"]));
    }

    /**
     * @covers Brickoo\Component\Annotation\Definition\AnnotationDefinitionTargetFilter::__construct
     * @covers Brickoo\Component\Annotation\Definition\AnnotationDefinitionTargetFilter::filter
     */
    public function testFilterAnnotationDefinitionsByTarget() {
        $definition = new AnnotationDefinition("annotation_1", Annotation::TARGET_CLASS);
        $annotationFilter = new AnnotationDefinitionTargetFilter(
            new Collection([
                $definition,
                new AnnotationDefinition("annotation_2", Annotation::TARGET_PROPERTY),
                new AnnotationDefinition("annotation_3", Annotation::TARGET_METHOD)
            ])
        );
        $filteredAnnotations = $annotationFilter->filter(Annotation::TARGET_CLASS);
        $this->assertSame($definition, $filteredAnnotations->current());
    }

}
