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

use ArrayIterator;
use Brickoo\Component\Annotation\Exception\InvalidTargetException;
use Brickoo\Component\Validation\Argument;
use IteratorAggregate;

/**
 * AnnotationReaderResult
 *
 * Implements an annotation reader result.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class AnnotationReaderResult implements IteratorAggregate {

    /** @var string */
    private $collectionName;

    /** @var string */
    private $className;

    /** @var array<integer, array<Annotation>> */
    private $annotations;

    /**
     * Class constructor.
     * @param string $collectionName
     * @param string $className
     */
    public function __construct($collectionName, $className) {
        Argument::isString($collectionName);
        Argument::isString($className);
        $this->collectionName = $collectionName;
        $this->className = $className;
        $this->annotations = [
            Annotation::TARGET_CLASS => [],
            Annotation::TARGET_METHOD => [],
            Annotation::TARGET_PROPERTY => []
        ];
    }

    /**
     * Returns the collection name.
     * @return string the collection name;
     */
    public function getCollectionName() {
        return $this->collectionName;
    }

    /**
     * Returns the target class name.
     * @return string the class name
     */
    public function getClassName() {
        return $this->className;
    }

    /**
     * Adds an annotation to the properly container matching the collection type.
     * @param \Brickoo\Component\Annotation\Annotation $annotation
     * @throws \Brickoo\Component\Annotation\Exception\InvalidTargetException
     * @return \Brickoo\Component\Annotation\AnnotationReaderResult
     */
    public function addAnnotation(Annotation $annotation) {
        $target = $annotation->getTarget();

        if (! $this->isTargetValid($target)) {
            throw new InvalidTargetException($target);
        }

        $this->annotations[$target][] = $annotation;
        return $this;
    }

    /**
     * Returns an array iterator containing all annotations.
     * @return \ArrayIterator containing all annotations
     */
    public function getIterator() {
        $mergedAnnotations = [];
        foreach ($this->annotations as $annotations) {
            $mergedAnnotations = array_merge($mergedAnnotations, $annotations);
        }
        return new ArrayIterator($mergedAnnotations);
    }

    /**
     * Returns an annotations iterator matching the target.
     * @param integer $target
     * @throws \InvalidArgumentException
     * @throws \Brickoo\Component\Annotation\Exception\InvalidTargetException
     * @return \ArrayIterator<Annotation>
     */
    public function getAnnotationsByTarget($target) {
        Argument::isInteger($target);

        if (! $this->isTargetValid($target)) {
            throw new InvalidTargetException($target);
        }

        return new ArrayIterator($this->annotations[$target]);
    }

    /**
     * Checks if the target is valid.
     * @param integer $target
     * @return boolean check result
     */
    private function isTargetValid($target) {
        return isset($this->annotations[$target]);
    }

}
