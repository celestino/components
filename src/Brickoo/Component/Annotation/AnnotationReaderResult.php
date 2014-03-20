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

use ArrayIterator,
    Brickoo\Component\Annotation\Exception\InvalidTargetTypeException,
    Brickoo\Component\Validation\Argument,
    IteratorAggregate;

/**
 * AnnotationReaderResult
 *
 * Implements as annotation reader result.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class AnnotationReaderResult implements IteratorAggregate {

    /** @var array<TargetType, AnnotationCollection> */
    private $collections;

    public function __construct() {
        $this->collections = [
            AnnotationTargetTypes::TYPE_CLASS => [],
            AnnotationTargetTypes::TYPE_METHOD => [],
            AnnotationTargetTypes::TYPE_PROPERTY => []
        ];
    }

    /**
     * Adds a collection to the matching type.
     * @param \Brickoo\Component\Annotation\AnnotationCollection $collection
     * @throws \Brickoo\Component\Annotation\Exception\InvalidTargetTypeException
     * @return \Brickoo\Component\Annotation\AnnotationReaderResult
     */
    public function addCollection(AnnotationCollection $collection) {
        $targetType = $collection->getTarget()->getType();

        if (! $this->isTargetTypeValid($targetType)) {
            throw new InvalidTargetTypeException($targetType);
        }

        $this->collections[$targetType][] = $collection;
        return $this;
    }

    /**
     * Returns a collection matching the target type.
     * @param integer $targetType
     * @throws \InvalidArgumentException
     * @throws \Brickoo\Component\Annotation\Exception\InvalidTargetTypeException
     * @return \ArrayIterator<AnnotationCollection>
     */
    public function getCollectionsByTargetType($targetType) {
        Argument::IsInteger($targetType);

        if (! $this->isTargetTypeValid($targetType)) {
            throw new InvalidTargetTypeException($targetType);
        }

        return new ArrayIterator($this->collections[$targetType]);
    }

    /**
     * Returns ann array iterator containg all collections.
     * @return \ArrayIterator containing all collections
     */
    public function getIterator() {
        return new ArrayIterator($this->collections);
    }

    /**
     * Checks if the target type is valid.
     * @param integer $targetType
     * @return boolean check reuslt
     */
    private function isTargetTypeValid($targetType) {
        return array_key_exists($targetType, $this->collections);
    }

}