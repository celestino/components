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
     * @param \Brickoo\Component\Annotation\Definition\DefinitionCollection $collection
     * @param \Brickoo\Component\Annotation\AnnotationReaderResult $readerResult
     * @throws \Brickoo\Component\Annotation\Exception\MissingRequiredAnnotationException
     * @throws \Brickoo\Component\Annotation\Exception\MissingRequiredAnnotationParametersException
     * @return void
     */
    public function validate(DefinitionCollection $collection, AnnotationReaderResult $readerResult) {
        $targets = [Annotation::TARGET_CLASS, Annotation::TARGET_METHOD, Annotation::TARGET_PROPERTY];

        foreach ($targets as $annotationTarget) {
            $this->validateAnnotations(
                $collection->getAnnotationsDefinitionsByTarget($annotationTarget),
                $readerResult->getAnnotationsByTarget($annotationTarget)
            );
        }
    }

    /**
     * Validates the annotations definitions against the result.
     * @param ArrayIterator $definitions
     * @param ArrayIterator $results
     * @return void
     */
    private function validateAnnotations(ArrayIterator $definitions, ArrayIterator $results) {
        if (($annotationsDefinitions = $this->getRequiredAnnotationsDefinitions($definitions))) {
            $annotationsParameters = $this->getAnnotationsParameters($results);

            foreach ($annotationsDefinitions as $requiredAnnotation => $requiredParameters) {
                $this->checkAnnotationRequirements($requiredAnnotation, $requiredParameters, $annotationsParameters);
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
        foreach ($definitions as $annotationDefinition) {
            if ($annotationDefinition->isRequired() || $annotationDefinition->hasRequiredParameters()) {
                $requiredAnnotations[$annotationDefinition->getName()] = $annotationDefinition->getRequiredParameters();
            }
        }
        return $requiredAnnotations;
    }

    /**
     * Returns the available result annotations and their parameters.
     * @param \ArrayIterator $annotationsIterator
     * @return array<String, Array<mixed, mixed>> the result annotations and parameters
     */
    private function getAnnotationsParameters(ArrayIterator $annotationsIterator) {
        $annotations = [];
        foreach ($annotationsIterator as $annotation) {
            $annotations[$annotation->getName()] = $annotation->getValues();
        }
        return $annotations;
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
        return isset($readAnnotations[$annotationName]);
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
            if (! isset($readParameters[$parameter->getName()])) {
                $missingParameters[] = $parameter->getName();
            }
        }
        return $missingParameters;
    }

}
