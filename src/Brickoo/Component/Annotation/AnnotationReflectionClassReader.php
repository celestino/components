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

use Brickoo\Component\Annotation\Definition\DefinitionCollection;
use ReflectionClass;

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
     * @param \Brickoo\Component\Annotation\Definition\DefinitionCollection $collection
     * @param \ReflectionClass $reflectionClass
     * @return \Brickoo\Component\Annotation\AnnotationReaderResult
     */
    public function getAnnotations(DefinitionCollection $collection, ReflectionClass $reflectionClass) {
        $readerResult = new AnnotationReaderResult($collection->getName(), $reflectionClass->getName());
        $this->addClassAnnotations($readerResult, $collection, $reflectionClass);
        $this->addMethodsAnnotations($readerResult, $collection, $reflectionClass);
        $this->addPropertiesAnnotations($readerResult, $collection, $reflectionClass);
        return $readerResult;
    }

    /**
     * Adds class annotations to the reader result.
     * @param \Brickoo\Component\Annotation\AnnotationReaderResult $result
     * @param \Brickoo\Component\Annotation\Definition\DefinitionCollection $collection
     * @param \ReflectionClass $class
     * @return \Brickoo\Component\Annotation\AnnotationReflectionClassReader
     */
    private function addClassAnnotations(AnnotationReaderResult $result, DefinitionCollection $collection, ReflectionClass $class) {
        $this->setAnnotationWhiteList($collection, Annotation::TARGET_CLASS);
        $this->parseAnnotations($result, Annotation::TARGET_CLASS, $class->getName(), $class->getDocComment());
        return $this;
    }

    /**
     * Adds methods annotations to the reader result.
     * @param \Brickoo\Component\Annotation\AnnotationReaderResult $result
     * @param \Brickoo\Component\Annotation\Definition\DefinitionCollection $collection
     * @param \ReflectionClass $class
     * @return \Brickoo\Component\Annotation\AnnotationReflectionClassReader
     */
    private function addMethodsAnnotations(AnnotationReaderResult $result, DefinitionCollection $collection, ReflectionClass $class) {
        $this->setAnnotationWhiteList($collection, Annotation::TARGET_METHOD);
        $this->parseAnnotationList($result, $class, Annotation::TARGET_METHOD);
        return $this;
    }

    /**
     * Adds properties annotations to the reader result.
     * @param \Brickoo\Component\Annotation\AnnotationReaderResult $result
     * @param \Brickoo\Component\Annotation\Definition\DefinitionCollection $collection
     * @param \ReflectionClass $class
     * @return \Brickoo\Component\Annotation\AnnotationReflectionClassReader
     */
    private function addPropertiesAnnotations(AnnotationReaderResult $result, DefinitionCollection $collection, ReflectionClass $class) {
        $this->setAnnotationWhiteList($collection, Annotation::TARGET_PROPERTY);
        $this->parseAnnotationList($result, $class, Annotation::TARGET_PROPERTY);
        return $this;
    }

    /**
     * Set the annotation white list for an annotation type.
     * @param \Brickoo\Component\Annotation\Definition\DefinitionCollection $collection
     * @param integer $targetType
     * @return \Brickoo\Component\Annotation\AnnotationReflectionClassReader
     */
    private function setAnnotationWhiteList(DefinitionCollection $collection, $targetType) {
        $this->annotationParser->setAnnotationWhitelist($this->getAnnotationsNames(
            $collection->getAnnotationsDefinitionsByTarget($targetType)
        ));
        return $this;
    }

    /**
     * Parse the annotation list from class member of a type.
     * @param \Brickoo\Component\Annotation\AnnotationReaderResult $result
     * @param \ReflectionClass $reflectionClass
     * @param integer $targetType
     * @return \Brickoo\Component\Annotation\AnnotationReflectionClassReader
     */
    private function parseAnnotationList(AnnotationReaderResult $result, ReflectionClass $reflectionClass, $targetType) {
        $reflectionMemberList = [];

        switch ($targetType) {
            case Annotation::TARGET_METHOD:
                $reflectionMemberList = $reflectionClass->getMethods();
                break;
            case Annotation::TARGET_PROPERTY:
                $reflectionMemberList = $reflectionClass->getProperties();
                break;
        }

        foreach ($reflectionMemberList as $member) {
            $this->parseAnnotations(
                $result,
                $targetType,
                sprintf("%s::%s", $reflectionClass->getName(), $member->getName()),
                $member->getDocComment()
            );
        }
        return $this;
    }

    /**
     * Returns the annotations names.
     * @param \ArrayIterator $annotationsDefinitionsIterator
     * @return array the annotations names
     */
    private function getAnnotationsNames(\ArrayIterator $annotationsDefinitionsIterator) {
        $annotationsNames = [];
        foreach ($annotationsDefinitionsIterator as $annotationDefinition) {
            $annotationsNames[] = $annotationDefinition->getName();
        }
        return $annotationsNames;
    }

    /**
     * Parse the doc comments annotations.
     * @param AnnotationReaderResult $result
     * @param integer $target
     * @param string $targetLocation
     * @param string $docComment
     * @return \Brickoo\Component\Annotation\AnnotationReflectionClassReader
     */
    private function parseAnnotations(AnnotationReaderResult $result, $target, $targetLocation, $docComment) {
        if ($annotations = $this->annotationParser->parse($target, $targetLocation, $docComment)) {
            $this->addResultAnnotations($result, $annotations);
        }
        return $this;
    }

    /**
     * Adds the annotations to the result collection.
     * @param \Brickoo\Component\Annotation\AnnotationReaderResult $result
     * @param Annotation[] $annotations
     * @return \Brickoo\Component\Annotation\AnnotationReflectionClassReader
     */
    private function addResultAnnotations(AnnotationReaderResult $result, array $annotations) {
        foreach ($annotations as $annotation) {
            $result->addAnnotation($annotation);
        }
        return $this;
    }

}
