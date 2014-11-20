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

namespace Brickoo\Tests\Component\IoC\Definition;

use Brickoo\Component\IoC\Annotation\DependencyAnnotation;
use Brickoo\Component\IoC\Definition\DependencyDefinition;
use PHPUnit_Framework_TestCase;

/**
 * DependencyAnnotationTest
 *
 * Test suite for the DependencyAnnotation class.
 * @see Brickoo\Component\IoC\Annotation\DependencyAnnotation
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class DependencyAnnotationTest extends PHPUnit_Framework_TestCase {

    /** @covers Brickoo\Component\IoC\Annotation\DependencyAnnotation::getAnnotationDefinition */
    public function testGetAnnotationDefinition() {
        $dependencyAnnotation = new DependencyAnnotation();
        $annotationDefinition = $dependencyAnnotation->getAnnotationDefinition();
        $this->assertInstanceOf(
            "\\Brickoo\\Component\\Annotation\\Definition\\AnnotationDefinition",
            $annotationDefinition
        );
    }

    /**
     * @covers Brickoo\Component\IoC\Annotation\DependencyAnnotation::getDependencyDefinition
     * @covers Brickoo\Component\IoC\Annotation\DependencyAnnotation::getInjectionDefinition
     * @covers Brickoo\Component\IoC\Annotation\DependencyAnnotation::getInjectionTarget
     * @covers Brickoo\Component\IoC\Annotation\DependencyAnnotation::getInjectionTargetName
     *
     */
    public function testGetDependencyDefinition() {
        $readerResultFixture = include __DIR__ . "/Assets/ReaderResultFixture.php";
        $dependencyAnnotation = new DependencyAnnotation();
        $dependencyDefinition = $dependencyAnnotation->getDependencyDefinition(
            "\\SomeClass",
            DependencyDefinition::SCOPE_PROTOTYPE,
            $readerResultFixture
        );
        $this->assertInstanceOf(
            "\\Brickoo\\Component\\IoC\\Definition\\DependencyDefinition",
            $dependencyDefinition
        );
    }

}
