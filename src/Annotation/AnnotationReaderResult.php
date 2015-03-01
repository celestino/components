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

use ArrayIterator;
use Brickoo\Component\Annotation\Exception\InvalidTargetException;
use Brickoo\Component\Common\Assert;
use IteratorAggregate;

/**
 * AnnotationReaderResult
 *
 * Implements an annotation reader result.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class AnnotationReaderResult implements IteratorAggregate {

    /** @var string */
    private $className;

    /** @var array */
    private $annotations;

    /**
     * Class constructor.
     * @param string $className
     */
    public function __construct($className) {
        Assert::isString($className);
        $this->className = $className;
        $this->annotations = [
            Annotation::TARGET_CLASS => [],
            Annotation::TARGET_METHOD => [],
            Annotation::TARGET_PROPERTY => []
        ];
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

        if (!$this->isTargetValid($target)) {
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
        return new ArrayIterator(array_merge(
            $this->annotations[Annotation::TARGET_CLASS],
            $this->annotations[Annotation::TARGET_METHOD],
            $this->annotations[Annotation::TARGET_PROPERTY]
        ));
    }

    /**
     * Returns an annotations iterator matching the target.
     * @param integer $target
     * @throws \InvalidArgumentException
     * @throws \Brickoo\Component\Annotation\Exception\InvalidTargetException
     * @return \ArrayIterator
     */
    public function getAnnotationsByTarget($target) {
        Assert::isInteger($target);

        if (!$this->isTargetValid($target)) {
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
