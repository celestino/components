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

namespace Brickoo\Component\Annotation\Definition;

use ArrayIterator;
use Brickoo\Component\Annotation\Exception\AnnotationNotAvailableException;
use Brickoo\Component\Validation\Argument;
use Countable;
use IteratorAggregate;

/**
 * DefinitionCollection
 *
 * Implements a definition collection.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class DefinitionCollection  implements Countable, IteratorAggregate {

    /** @var string */
    private $uniqueName;

    /** @var array */
    private $annotationsContainer;

    /**
     * Class constructor.
     * @param string $uniqueName
     */
    public function __construct($uniqueName) {
        Argument::isString($uniqueName);
        $this->uniqueName = $uniqueName;
        $this->annotationsContainer = [];
    }

    /**
     * Returns the collection unique name.
     * @return string the collection name
     */
    public function getName() {
        return $this->uniqueName;
    }

    /**
     * Returns the first annotation from the collection stack.
     * The annotation will be removed from stack.
     * @throws \Brickoo\Component\Annotation\Exception\AnnotationNotAvailableException
     * @return \Brickoo\Component\Annotation\Definition\AnnotationDefinition the first annotation definition on stack
     */
    public function shift() {
        if ($this->isEmpty()) {
            throw new AnnotationNotAvailableException();
        }
        return array_shift($this->annotationsContainer);
    }

    /**
     * Returns the last annotation from collection stack.
     * The annotation will be removed from stack.
     * @throws \Brickoo\Component\Annotation\Exception\AnnotationNotAvailableException
     * @return \Brickoo\Component\Annotation\Definition\AnnotationDefinition the last annotation definition
     */
    public function pop() {
        if ($this->isEmpty()) {
            throw new AnnotationNotAvailableException();
        }
        return array_pop($this->annotationsContainer);
    }

    /**
     * Push an annotation into the stack.
     * @param \Brickoo\Component\Annotation\Definition\AnnotationDefinition $annotation
     * @return \Brickoo\Component\Annotation\Definition\DefinitionCollection
     */
    public function push(AnnotationDefinition $annotation) {
        $this->annotationsContainer[] = $annotation;
        return $this;
    }

    /**
     * Returns all listened annotations as an iterator.
     * @return \ArrayIterator
     */
    public function getIterator() {
        return new ArrayIterator($this->annotationsContainer);
    }

    /**
     * Checks if the collection has annotations.
     * @return boolean check result
     */
    public function isEmpty() {
        return empty($this->annotationsContainer);
    }

    /** {@inheritDoc} */
    public function count() {
        return count($this->annotationsContainer);
    }

    /**
     * Returns the annotations definitions matching the target.
     * @param integer $target
     * @return \ArrayIterator containing annotations definitions
     */
    public function getAnnotationsDefinitionsByTarget($target) {
        Argument::isInteger($target);
        $annotationsDefinitions = [];
        foreach ($this as $annotationDefinition) {
            if ($annotationDefinition->isTarget($target)) {
                $annotationsDefinitions[] = $annotationDefinition;
            }
        }
        return new ArrayIterator($annotationsDefinitions);
    }

}
