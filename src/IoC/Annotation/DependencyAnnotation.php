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

namespace Brickoo\Component\IoC\Annotation;

use Brickoo\Component\Annotation\Annotation;
use Brickoo\Component\Annotation\AnnotationReaderResult;
use Brickoo\Component\Annotation\Definition\AnnotationDefinition;
use Brickoo\Component\IoC\Definition\ArgumentDefinition;
use Brickoo\Component\IoC\Definition\Container\ArgumentDefinitionContainer;
use Brickoo\Component\IoC\Definition\Container\InjectionDefinitionContainer;
use Brickoo\Component\IoC\Definition\DependencyDefinition;
use Brickoo\Component\IoC\Definition\InjectionDefinition;

/**
 * DependencyAnnotation
 *
 * Implements a dependency annotation for dependency injection usage.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class DependencyAnnotation {

    /** Dependency annotation identifier */
    const DEPENDENCY_ANNOTATION = "Dependency";

    /** Regular expression for matching the class target name */
    const TARGET_NAME_REGEX = "~^\\\\?[a-zA-Z_\\x7f-\\xff][\\w\\x7f-\\xff\\\\]*\\:\\:(?<target>([a-zA-Z_\\x7f-\\xff]+))$~";

    /**
     * Returns the dependency annotation definition.
     * @param integer $target annotation target
     * @param boolean $required require annotation
     * @return \Brickoo\Component\Annotation\Definition\AnnotationDefinition
     */
    public function getAnnotationDefinition($target = Annotation::TARGET_CLASS, $required = true) {
        return new AnnotationDefinition(self::DEPENDENCY_ANNOTATION, $target, $required);
    }

    /**
     * Returns the dependency definition.
     * @param $dependency
     * @param integer $scope 1:SINGLETON 2:PROTOTYPE
     * @param \Brickoo\Component\Annotation\AnnotationReaderResult $readerResults
     * @return \Brickoo\Component\IoC\Definition\DependencyDefinition
     */
    public function getDependencyDefinition($dependency, $scope, AnnotationReaderResult $readerResults) {
        $injectionDefinitions = [];
        foreach ($readerResults as $annotation) {
            if ($annotation->getName() == self::DEPENDENCY_ANNOTATION) {
                $injectionDefinitions[] = $this->getInjectionDefinition($annotation);
            }
        }

        return new DependencyDefinition($dependency, $scope, null,
            new InjectionDefinitionContainer($injectionDefinitions)
        );
    }

    /**
     * Returns the injection definition from the annotation.
     * @param \Brickoo\Component\Annotation\Annotation $annotation
     * @return \Brickoo\Component\IoC\Definition\InjectionDefinition
     */
    private function getInjectionDefinition(Annotation $annotation) {
        $argumentsDefinitions = [];
        foreach ($annotation->getValues() as $argument) {
            $argumentsDefinitions[] = new ArgumentDefinition($argument);
        }

        return new InjectionDefinition(
            $this->getInjectionTarget($annotation),
            $this->getInjectionTargetName($annotation),
            new ArgumentDefinitionContainer($argumentsDefinitions)
        );
    }

    /**
     * Returns the injection target.
     * @param \Brickoo\Component\Annotation\Annotation $annotation
     * @return string the injection target
     */
    private function getInjectionTarget(Annotation $annotation) {
        $annotationTargetsMapping = [
            Annotation::TARGET_CLASS => InjectionDefinition::TARGET_CONSTRUCTOR,
            Annotation::TARGET_METHOD => InjectionDefinition::TARGET_METHOD,
            Annotation::TARGET_PROPERTY => InjectionDefinition::TARGET_PROPERTY
        ];

        return $annotationTargetsMapping[$annotation->getTarget()];
    }

    /**
     * Returns the injection target (method,property) name.
     * @param \Brickoo\Component\Annotation\Annotation $annotation
     * @return string|null the target name otherwise null on failure
     */
    private function getInjectionTargetName(Annotation $annotation) {
        if ($annotation->getTarget() == Annotation::TARGET_CLASS) {
            return $annotation->getTargetLocation();
        }

        $targetLocation = null;
        if (preg_match(
            self::TARGET_NAME_REGEX,
            $annotation->getTargetLocation(),
            $matches)) {
                $targetLocation = $matches["target"];
        }
        return $targetLocation;
    }

}
