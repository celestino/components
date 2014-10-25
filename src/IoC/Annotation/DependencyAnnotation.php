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
            $matches
        )) {
            $targetLocation = $matches["target"];
        }
        return $targetLocation;
    }

}
