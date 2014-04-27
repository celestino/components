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
    Brickoo\Component\Annotation\Definition\DefinitionContainer,
    Brickoo\Component\Annotation\Definition\DefinitionCollection,
    Brickoo\Component\Annotation\Exception\MissingRequiredAnnotationException,
    Brickoo\Component\Annotation\Exception\MissingRequiredAnnotationParametersException;

/**
 * ReaderResultValidator
 *
 * Implements a reader result validator.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class ReaderResultValidator {

    /**
     * Validates a reader result against the provided definition.
     * @param \Brickoo\Component\Annotation\Definition\DefinitionContainer $definition
     * @param \Brickoo\Component\Annotation\AnnotationReaderResult $readerResult
     * @throws \Brickoo\Component\Annotation\Exception\MissingRequiredAnnotationException
     * @throws \Brickoo\Component\Annotation\Exception\MissingRequiredAnnotationParametersException
     * @return void
     */
    public function validate(DefinitionContainer $definition, AnnotationReaderResult $readerResult) {
        $types = [AnnotationTarget::TYPE_CLASS, AnnotationTarget::TYPE_METHOD, AnnotationTarget::TYPE_PROPERTY];

        foreach ($types as $annotationTargetType) {
            $this->validateCollections(
                $definition->getCollectionsByTargetType($annotationTargetType),
                $readerResult->getCollectionsByTargetType($annotationTargetType)
            );
        }
    }

    /**
     * Validates the definition collections against the result collections.
     * @param ArrayIterator $definitions
     * @param ArrayIterator $results
     * @return void
     */
    private function validateCollections(ArrayIterator $definitions, ArrayIterator $results) {
        if (($annotationsDefinitions = $this->getRequiredAnnotationsDefinitions($definitions))) {
            $annotationsRead = $this->getReadAnnotationsParameters($results);

            foreach ($annotationsDefinitions as $requiredAnnotation => $requiredParameters) {
                $this->checkAnnotationRequirements($requiredAnnotation, $requiredParameters, $annotationsRead);
            }
        }
    }

    /**
     * Returns the required annotations and their parameters.
     * @param ArrayIterator $definitions
     * @return array<String, ParameterDefinition> the required annotations definitions
     */
    private function getRequiredAnnotationsDefinitions(ArrayIterator $definitions) {
        $requiredAnnotations = [];
        foreach ($definitions as $definitionCollection) {
            $this->collectRequiredAnnotations($definitionCollection, $requiredAnnotations);
        }
        return $requiredAnnotations;
    }

    /**
     * Returns the collected required annotations and their parameters.
     * @param \Brickoo\Component\Annotation\Definition\DefinitionCollection $collection
     * @param array &$requiredAnnotations
     * @return array<String, ParameterDefinition> the required annotations definitions
     */
    private function collectRequiredAnnotations(DefinitionCollection $collection, array &$requiredAnnotations) {
        foreach ($collection as $annotation) {
            if ($annotation->isRequired() || $annotation->hasRequiredParameters()) {
                $requiredAnnotations[$annotation->getName()] = $annotation->getRequiredParameters();
            }
        }
    }

    /**
     * Returns the available result annotations and their parameters.
     * @param \ArrayIterator $readerResults
     * @return array<String, Array> the result annotations and parameters
     */
    private function getReadAnnotationsParameters(ArrayIterator $readerResults) {
        $readAnnotations = [];
        foreach ($readerResults as $collection) {
            $this->collectReadAnnotations($collection, $readAnnotations);
        }
        return $readAnnotations;
    }

    /**
     * Returns the collected result annotations and their parameters.
     * @param \Brickoo\Component\Annotation\AnnotationCollection $collection
     * @param array $readAnnotations
     * @return array<String, Array> the result annotations and parameters
     */
    private function collectReadAnnotations(AnnotationCollection $collection, array &$readAnnotations) {
        foreach($collection as $annotation) {
            $readAnnotations[$annotation->getName()] = $annotation->getValues();
        }
    }

    /**
     * Checks if the read annotations matches the definition requirements.
     * @param string $requiredAnnotation
     * @param array $requiredParameters
     * @param array $annotationsRead
     * @throws \Brickoo\Component\Annotation\Exception\MissingRequiredAnnotationException
     * @throws \Brickoo\Component\Annotation\Exception\MissingRequiredAnnotationParametersException
     * @return void
     */
    private function checkAnnotationRequirements($requiredAnnotation, array $requiredParameters, array $annotationsRead) {
        if (! $this->hasRequiredAnnotation($requiredAnnotation, $annotationsRead)) {
            throw new MissingRequiredAnnotationException($requiredAnnotation);
        }
        if (($missingParameters = $this->getMissingParameters($requiredParameters, $annotationsRead[$requiredAnnotation]))) {
            throw new MissingRequiredAnnotationParametersException($requiredAnnotation, $missingParameters);
        }
    }

    /**
     * Checks if the required annotation is available in the result.
     * @param string $annotationName
     * @param array $readAnnotations
     * @return boolean check result
     */
    private function hasRequiredAnnotation($annotationName, array $readAnnotations) {
        return array_key_exists($annotationName, $readAnnotations);
    }

    /**
     * Returns the missing required parameters if any.
     * @param array $requiredParameters
     * @param array $readParameters
     * @return array<String> the missing parameters
     */
    private function getMissingParameters(array $requiredParameters, array $readParameters) {
        $missingParameters = [];
        foreach ($requiredParameters as $parameter) {
            if (! array_key_exists($parameter->getName(), $readParameters)) {
                $missingParameters[] = $parameter->getName();
            }
        }
        return $missingParameters;
    }

}
