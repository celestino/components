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

namespace Brickoo\Component\Annotation;

use Brickoo\Component\Annotation\Definition\AnnotationDefinitionTargetFilter;
use Brickoo\Component\Common\Collection;
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

    /** @var \Brickoo\Component\Annotation\Definition\AnnotationDefinitionTargetFilter */
    private $annotationTargetFilter;

    /**
     * Class constructor.
     * @param \Brickoo\Component\Annotation\AnnotationParser $annotationParser
     */
    public function __construct(AnnotationParser $annotationParser) {
        $this->annotationParser = $annotationParser;
    }

    /**
     * Returns the read annotations.
     * @param \Brickoo\Component\Common\Collection $collection
     * @param \ReflectionClass $reflectionClass
     * @return \Brickoo\Component\Annotation\AnnotationReaderResult
     */
    public function getAnnotations(Collection $collection, ReflectionClass $reflectionClass) {
        $this->annotationTargetFilter = new AnnotationDefinitionTargetFilter(($collection));
        $readerResult = new AnnotationReaderResult($reflectionClass->getName());
        $this->addClassAnnotations($readerResult, $collection, $reflectionClass);
        $this->addMethodsAnnotations($readerResult, $collection, $reflectionClass);
        $this->addPropertiesAnnotations($readerResult, $collection, $reflectionClass);
        return $readerResult;
    }

    /**
     * Adds class annotations to the reader result.
     * @param \Brickoo\Component\Annotation\AnnotationReaderResult $result
     * @param \Brickoo\Component\Common\Collection $collection
     * @param \ReflectionClass $class
     * @return \Brickoo\Component\Annotation\AnnotationReflectionClassReader
     */
    private function addClassAnnotations(AnnotationReaderResult $result, Collection $collection, ReflectionClass $class) {
        $this->setAnnotationWhiteList($collection, Annotation::TARGET_CLASS);
        $this->parseAnnotations($result, Annotation::TARGET_CLASS, $class->getName(), $class->getDocComment());
        return $this;
    }

    /**
     * Adds methods annotations to the reader result.
     * @param \Brickoo\Component\Annotation\AnnotationReaderResult $result
     * @param \Brickoo\Component\Common\Collection $collection
     * @param \ReflectionClass $class
     * @return \Brickoo\Component\Annotation\AnnotationReflectionClassReader
     */
    private function addMethodsAnnotations(AnnotationReaderResult $result, Collection $collection, ReflectionClass $class) {
        $this->setAnnotationWhiteList($collection, Annotation::TARGET_METHOD);
        $this->parseAnnotationList($result, $class, Annotation::TARGET_METHOD);
        return $this;
    }

    /**
     * Adds properties annotations to the reader result.
     * @param \Brickoo\Component\Annotation\AnnotationReaderResult $result
     * @param \Brickoo\Component\Common\Collection $collection
     * @param \ReflectionClass $class
     * @return \Brickoo\Component\Annotation\AnnotationReflectionClassReader
     */
    private function addPropertiesAnnotations(AnnotationReaderResult $result, Collection $collection, ReflectionClass $class) {
        $this->setAnnotationWhiteList($collection, Annotation::TARGET_PROPERTY);
        $this->parseAnnotationList($result, $class, Annotation::TARGET_PROPERTY);
        return $this;
    }

    /**
     * Set the annotation white list for an annotation type.
     * @param \Brickoo\Component\Common\Collection $collection
     * @param integer $targetType
     * @return \Brickoo\Component\Annotation\AnnotationReflectionClassReader
     */
    private function setAnnotationWhiteList(Collection $collection, $targetType) {
        $this->annotationParser->setAnnotationWhitelist(
            $this->getAnnotationsNames(
                $this->annotationTargetFilter->filter($targetType)
            )
        );
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
        $reflectionMemberList = $this->getReflectionMemberList($reflectionClass, $targetType);
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
     * Get the reflection member list.
     * @param ReflectionClass $reflectionClass
     * @param integer $targetType
     * @return array the reflection member list
     */
    private function getReflectionMemberList(ReflectionClass $reflectionClass, $targetType) {
        $reflectionMemberList = [];

        switch ($targetType) {
            case Annotation::TARGET_METHOD:
                $reflectionMemberList = $reflectionClass->getMethods();
                break;
            case Annotation::TARGET_PROPERTY:
                $reflectionMemberList = $reflectionClass->getProperties();
                break;
        }

        return $reflectionMemberList;
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
