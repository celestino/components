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

use Brickoo\Component\Validation\Argument;

/**
 * AnnotationParser
 *
 * Implements an annotation parser based on the
 * documentation block of a class, method or property.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class AnnotationParser {

    /** @const regular expressions */
    const REGEX_ANNOTATION = "~%s(?<%s>[\\w]+)[^%s(]*\\(\\s*(?<%s>[^%s\\)\\(]*)\\s*\\)~";
    const REGEX_NESTED_ANNOTATIONS = "~%s(?<%s>[\\w]+)[^%s{]*\\({\\s*(?<%s>[^}]*)\\s*}\\)~";
    const REGEX_PARAMETER = "~((?<%s>\\w+)\\s*=)?\\s*(?<%s>(\"[^\"]*\")|(\\[[^\\]]*\\])|[0-9\\.]+|true|false)~";
    const REGEX_VALUE = "~((?<%s>\\w+)\\s*=)?\\s*(?<%s>('[^']*')|[0-9\\.]+|true|false)~";

    /** @const regular expressions capture groups  */
    const REGEX_CAPTURE_ANNOTATION = "annotation";
    const REGEX_CAPTURE_VALUES = "values";
    const REGEX_CAPTURE_PARAM = "param";
    const REGEX_CAPTURE_VALUE = "value";

    /** @var string */
    private $annotationPrefix;

    /** @var array */
    private $annotationWhitelist;

    /**
     * Class constructor.
     * @param string $annotationPrefix
     */
    public function __construct($annotationPrefix = "@") {
        Argument::IsString($annotationPrefix);
        $this->annotationPrefix = $annotationPrefix;
        $this->annotationWhitelist = [];
    }

    /**
     * Changes the annotation prefix.
     * @param string $annotationPrefix
     * @return \Brickoo\Component\Annotation\AnnotationParser
     */
    public function setAnnotationPrefix($annotationPrefix) {
        Argument::IsString($annotationPrefix);
        $this->annotationPrefix = $annotationPrefix;
        return $this;
    }

    /**
     * Changes the annotations whitelist.
     * @param array $whitelist
     * @return \Brickoo\Component\Annotation\AnnotationParser
     */
    public function setAnnotationWhitelist(array $whitelist) {
        $this->annotationWhitelist = $whitelist;
        return $this;
    }

    /**
     * Parse the document comment to extract annotations.
     * @param \Brickoo\Component\Annotation\AnnotationTarget $target
     * @param string $docComment
     * @throws \InvalidArgumentException
     * @return \Brickoo\Component\Annotation\AnnotationCollection
     */
    public function parse(AnnotationTarget $target, $docComment) {
        Argument::IsString($docComment);

        $annotationCollection = new AnnotationCollection($target);
        if (($annotationsMatches = $this->getAnnotationsMatches($this->annotationPrefix, $docComment))
            && (! empty($annotationsMatches[self::REGEX_CAPTURE_ANNOTATION]))
        ){
            $this->addAnnotations(
                $annotationCollection,
                $this->getAnnotationList($annotationsMatches)
            );
        }
        return $annotationCollection;
    }

    /**
     * Returns the matches containing annotations.
     * @param string $annotationPrefix
     * @param string $docComment
     * @return array the annotations matches
     */
    private function getAnnotationsMatches($annotationPrefix, $docComment) {
        $matches = null;
        preg_match_all(
            sprintf(self::REGEX_ANNOTATION,
                preg_quote($annotationPrefix, "~"),
                self::REGEX_CAPTURE_ANNOTATION,
                preg_quote($annotationPrefix, "~"),
                self::REGEX_CAPTURE_VALUES,
                preg_quote($annotationPrefix, "~")
            ),
            $docComment, $matches
        );
        return $matches;
    }

    /**
     * Returns an array list containing the annotation name and values.
     * @param array $annotationsMatches
     * @return array list of annotations
     */
    private function getAnnotationList(array $annotationsMatches) {
        $annotationList = [];
        foreach ($annotationsMatches[self::REGEX_CAPTURE_ANNOTATION] as $currentIndex => $annotation) {
            if ($this->isAnnotationInWhitelist($annotation)) {
               $annotationList[] = [
                   "name" => $annotation,
                   "values" => $this->getAnnotationValues($currentIndex, $annotationsMatches)
               ];
            }
        }
        return $annotationList;
    }

    /**
     * Checks if the annotation is in the whitelist.
     * @param string $annotation
     * @return boolean check result
     */
    private function isAnnotationInWhitelist($annotation) {
        return in_array($annotation, $this->annotationWhitelist);
    }

    /**
     * Returns the annotations values.
     * @param string $annotationIndex
     * @param array $annotationsMatches
     * @return array the annotation values
     */
    private function getAnnotationValues($annotationIndex, array $annotationsMatches) {
        $valuesString = $annotationsMatches[self::REGEX_CAPTURE_VALUES][$annotationIndex];
        $valuesRegex = sprintf(self::REGEX_PARAMETER, self::REGEX_CAPTURE_PARAM, self::REGEX_CAPTURE_VALUE);
        return $this->getParameterValues($valuesString, $valuesRegex);
    }

    /**
     * Returns the  parameters values pairs.
     * @param string $valuesString
     * @param string $valuesRegex
     * @return array the parameters values pairs
     */
    private function getParameterValues($valuesString, $valuesRegex) {
        $values = null;
        $parameterValues = [];
        if ((! empty($valuesString)) && preg_match_all($valuesRegex, $valuesString, $values) !== false) {
            foreach ($values[self::REGEX_CAPTURE_PARAM] as $currentIndex => $param) {
                $param = $param ?: $currentIndex;
                $parameterValues[$param] = $this->convertValue($values[self::REGEX_CAPTURE_VALUE][$currentIndex]);
            }
        }
        return $parameterValues;
    }

    /**
     * Converts the value to string or array.
     * @param string $value
     * @return mixed string or array value
     */
    private function convertValue($value) {
        $value = trim($value, "\"'");
        if (preg_match("~^\[.+\]$~", $value) == 0) {
            return $this->transformScalar($value);
        }

        $valuesString = trim($value, "[]");
        $valuesRegex = sprintf(self::REGEX_VALUE, self::REGEX_CAPTURE_PARAM, self::REGEX_CAPTURE_VALUE);
        return $this->getParameterValues($valuesString, $valuesRegex);
    }

    /**
     * Transforms the string to appropriate scalar.
     * @param string $value
     * @return mixed transformed value
     */
    private function transformScalar($value) {
        switch ($value) {
            case is_numeric($value):
                $value = strpos($value, ".") ? floatval($value) : intval($value);
                break;
            case $value === "true":
            case $value === "false":
                $value = $value === "true" ? true : false;
                break;
        }
        return $value;
    }

    /**
     * Adds annotations into the annotation collection.
     * @param \Brickoo\Component\Annotation\AnnotationCollection $collection
     * @param array $annotationList
     * @return void
     */
    private function addAnnotations(AnnotationCollection $collection, array $annotationList) {
        foreach ($annotationList as $annotation) {
            $collection->push($this->createAnnotation($annotation));
        }
    }

    /**
     * Creates an annotation based on the target and definition.
     * @param array $annotation
     * @return \Brickoo\Component\Annotation\Annotation
     */
    private function createAnnotation(array $annotation) {
        return new Annotation($annotation["name"], $annotation["values"]);
    }

}
