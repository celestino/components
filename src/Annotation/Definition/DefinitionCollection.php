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
