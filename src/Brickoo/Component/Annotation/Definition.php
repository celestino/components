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
    Brickoo\Component\Annotation\DefinitionCollection,
    Brickoo\Component\Validation\Argument,
    IteratorAggregate;

/**
 * AnnotationDefinition
 *
 * Implements an annotation definition.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class Definition implements IteratorAggregate {

    /** @var array<AnnotationDefinitionCollection> */
    private $definitionCollections;

    public function __construct() {
        $this->definitionCollections = [];
    }

    /**
     * Adds a collection to the list.
     * @param \Brickoo\Component\Annotation\DefinitionCollection $collection
     * @return \Brickoo\Component\Annotation\Definition
     */
    public function addCollection(DefinitionCollection $collection) {
        $this->definitionCollections[] = $collection;
        return $this;
    }

    /**
     * Checks if the definition has collections.
     * @return boolean check result
     */
    public function hasCollections() {
        return empty($this->definitionCollections) == false;
    }

    /**
     * Returns the collections matching the target type.
     * @param integer $targetType
     * @return \ArrayIterator containing definition collections
     */
    public function getCollectionsByTargetType($targetType) {
        Argument::IsInteger($targetType);
        $collections = [];
        foreach ($this as $collection) {
            if ($collection->isTypeOf($targetType)) {
                $collections[] = $collection;
            }
        }
        return new ArrayIterator($collections);
    }

    /**
     * Returns an array iterator with definition collections.
     * @return \ArrayIterator
     */
    public function getIterator() {
        return new ArrayIterator($this->definitionCollections);
    }

}