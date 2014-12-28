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
use Brickoo\Component\Annotation\Definition\DefinitionCollection;
use Brickoo\Component\Annotation\Exception\MissingRequiredAnnotationException;
use Brickoo\Component\Annotation\Exception\MissingRequiredAnnotationParametersException;

/**
 * AnnotationReaderResultValidator
 *
 * Implements a reader result validator.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class AnnotationReaderResultValidator {

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
        if (($requiredAnnotationsParameters = $this->getRequiredAnnotationsParameters($definitions))) {
            $annotationsValues = $this->getAnnotationsValues($results);

            foreach ($requiredAnnotationsParameters as $requiredAnnotation => $requiredParameters) {
                $this->checkAnnotationRequirements($requiredAnnotation, $requiredParameters, $annotationsValues);
            }
        }
    }

    /**
     * Returns the required annotations and their parameters.
     * @param ArrayIterator $definitions
     * @return array required annotations definitions
     */
    private function getRequiredAnnotationsParameters(ArrayIterator $definitions) {
        $requiredAnnotations = [];
        foreach ($definitions as $annotationDefinition) {
            if ($annotationDefinition->isRequired() || $annotationDefinition->hasRequiredParameters()) {
                $requiredAnnotations[$annotationDefinition->getName()] = $annotationDefinition->getRequiredParameters();
            }
        }
        return $requiredAnnotations;
    }

    /**
     * Returns the available result annotations and their values.
     * @param \ArrayIterator $annotationsIterator
     * @return array annotation values
     */
    private function getAnnotationsValues(ArrayIterator $annotationsIterator) {
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
