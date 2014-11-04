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
