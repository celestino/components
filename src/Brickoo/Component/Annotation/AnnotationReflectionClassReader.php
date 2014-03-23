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

namespace Brickoo\Component\Annotation;

use Brickoo\Component\Annotation\Definition,
    Brickoo\Component\Annotation\Definition\TargetDefinition,
    ReflectionClass;

/**
 * AnnotationReflectionClassReader
 *
 * Implements an annotation reader based on the reflection API.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class AnnotationReflectionClassReader {

    /** @var \Brickoo\Component\Annotation\AnnotationParser */
    private $annotationParser;

    /**
     * Class constructor.
     * @param \Brickoo\Component\Annotation\AnnotationParser $annotationParser
     */
    public function __construct(AnnotationParser $annotationParser) {
        $this->annotationParser = $annotationParser;
    }

    /**
     * Returns the read annotations.
     * @param \Brickoo\Component\Annotation\Definition $definition
     * @param \ReflectionClass $reflectionClass
     * @return \Brickoo\Component\Annotation\AnnotationClassReaderResult
     */
    public function getAnnotations(Definition $definition, ReflectionClass $reflectionClass) {
        $readerResult = new AnnotationClassReaderResult($definition->getName(), $reflectionClass->getName());
        $this->addClassAnnotations($readerResult, $definition, $reflectionClass);
        $this->addMethodsAnnotations($readerResult, $definition, $reflectionClass);
        $this->addPropertiesAnnotations($readerResult, $definition, $reflectionClass);
        return $readerResult;
    }

    /**
     * Adds read class annotations to result collection
     * @param \Brickoo\Component\Annotation\AnnotationClassReaderResult $result
     * @param \Brickoo\Component\Annotation\Definition $definition
     * @param ReflectionClass $class
     * @return \Brickoo\Component\Annotation\AnnotationReflectionClassReader
     */
    private function addClassAnnotations(AnnotationClassReaderResult $result, Definition $definition, ReflectionClass $class) {
        $this->annotationParser->setAnnotationWhitelist($this->getAnnotationsNames(
            $definition->getCollectionsByTargetType(TargetDefinition::TYPE_CLASS)
        ));
        $result->addCollection($this->annotationParser->parse(
            new AnnotationTarget(AnnotationTarget::TYPE_CLASS, $class->getName()),
            $class->getDocComment()
        ));
        return $this;
    }

    /**
     * Adds read methods annotations to result collection.
     * @param \Brickoo\Component\Annotation\AnnotationClassReaderResult $result
     * @param \Brickoo\Component\Annotation\Definition $definition
     * @param ReflectionClass $class
     * @return \Brickoo\Component\Annotation\AnnotationReflectionClassReader
     */
    private function addMethodsAnnotations(AnnotationClassReaderResult $result, Definition $definition, ReflectionClass $class) {
        $this->annotationParser->setAnnotationWhitelist($this->getAnnotationsNames(
            $definition->getCollectionsByTargetType(TargetDefinition::TYPE_METHOD)
        ));

        foreach ($class->getMethods() as $method) {
            $result->addCollection($this->annotationParser->parse(
                new AnnotationTarget(AnnotationTarget::TYPE_METHOD, $class->getName(), $method->getName()),
                $method->getDocComment()
            ));
        }
        return $this;
    }

    /**
     * Adds read properties annotations to result collection.
     * @param \Brickoo\Component\Annotation\AnnotationClassReaderResult $result
     * @param \Brickoo\Component\Annotation\Definition $definition
     * @param ReflectionClass $class
     * @return \Brickoo\Component\Annotation\AnnotationReflectionClassReader
     */
    private function addPropertiesAnnotations(AnnotationClassReaderResult $result, Definition $definition, ReflectionClass $class) {
        $this->annotationParser->setAnnotationWhitelist($this->getAnnotationsNames(
            $definition->getCollectionsByTargetType(TargetDefinition::TYPE_PROPERTY)
        ));

        foreach ($class->getProperties() as $property) {
            $result->addCollection($this->annotationParser->parse(
                new AnnotationTarget(AnnotationTarget::TYPE_PROPERTY, $class->getName(), $property->getName()),
                $property->getDocComment()
            ));
        }
        return $this;
    }

    /**
     * Returns the annotations names.
     * @param \ArrayIterator $definitionCollections
     * @return array the annotations names
     */
    private function getAnnotationsNames(\ArrayIterator $definitionCollections) {
        $annotationsNames = [];
        foreach ($definitionCollections as $annotationDefinitions) {
            foreach ($annotationDefinitions as $annotation) {
                $annotationsNames[] = $annotation->getName();
            }
        }
        return $annotationsNames;
    }

}