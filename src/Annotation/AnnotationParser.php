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

use Brickoo\Component\Common\Assert;

/**
 * AnnotationParser
 *
 * Implements an annotation parser based on the
 * documentation block of a class, method or property.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class AnnotationParser {

    /** @const regular expressions templates */
    const REGEX_ANNOTATION = "~[^\"]%s(?<%s>[\\w]+)[^%s(]*\\(\\s*(?<%s>[^\\)\\(]*)\\s*\\)~";
    const REGEX_PARAMETER = "~((?<%s>\\w+)\\s*=)?\\s*(?<%s>(\"[^\"]*\")|[0-9\\.]+|true|false)~";
    const REGEX_VALUE = "~((?<%s>\\w+)\\s*=)?\\s*(?<%s>('[^']*')|[0-9\\.]+|true|false)~";
    const REGEX_ARRAY = "~^\\[\\[.+\\]\\]$~";

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
        Assert::isString($annotationPrefix);
        $this->annotationPrefix = $annotationPrefix;
        $this->annotationWhitelist = [];
    }

    /**
     * Changes the annotation prefix.
     * @param string $annotationPrefix
     * @return \Brickoo\Component\Annotation\AnnotationParser
     */
    public function setAnnotationPrefix($annotationPrefix) {
        Assert::isString($annotationPrefix);
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
     * @param string $target
     * @param string $targetLocation
     * @param string $docComment
     * @throws \InvalidArgumentException
     * @return \Brickoo\Component\Annotation\Annotation[]
     */
    public function parse($target, $targetLocation, $docComment) {
        Assert::isInteger($target);
        Assert::isString($targetLocation);
        Assert::isString($docComment);

        $annotations = null;
        if (($annotationsMatches = $this->getAnnotationsMatches($this->annotationPrefix, $docComment))
            && (! empty($annotationsMatches[self::REGEX_CAPTURE_ANNOTATION]))
        ){
            $annotations = $this->convertAnnotations($target, $targetLocation, $this->getAnnotationList($annotationsMatches));
        }
        return $annotations ?: [];
    }

    /**
     * Returns the matches containing annotations.
     * @param string $annotationPrefix
     * @param string $docComment
     * @return array
     */
    private function getAnnotationsMatches($annotationPrefix, $docComment) {
        $matches = [];
        preg_match_all(
            sprintf(self::REGEX_ANNOTATION,
                preg_quote($annotationPrefix, "~"),
                self::REGEX_CAPTURE_ANNOTATION,
                preg_quote($annotationPrefix, "~"),
                self::REGEX_CAPTURE_VALUES
            ),
            $docComment,
            $matches
        );
        return $matches;
    }

    /**
     * Returns a list containing the annotation name and values.
     * @param array $annotationsMatches
     * @return array
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
     * @return boolean
     */
    private function isAnnotationInWhitelist($annotation) {
        return in_array($annotation, $this->annotationWhitelist);
    }

    /**
     * Returns the annotations values.
     * @param string $annotationIndex
     * @param array $annotationsMatches
     * @return array
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
     * @return array
     */
    private function getParameterValues($valuesString, $valuesRegex) {
        $values = [];
        $parameterValues = [];
        if ((! empty($valuesString))
            && preg_match_all($valuesRegex, $valuesString, $values) !== false
            && $values !== null) {
            $this->attachParameterValues($parameterValues, $values);
        }
        return $parameterValues;
    }

    /**
     * Attach the extracted parameters values.
     * @param array &$parameterValues
     * @param array $values
     * @return void
     */
    private function attachParameterValues(&$parameterValues, array $values) {
        if (isset($values[self::REGEX_CAPTURE_PARAM])) {
            foreach ($values[self::REGEX_CAPTURE_PARAM] as $currentIndex => $param) {
                $param = $param ?: $currentIndex;
                $parameterValues[$param] = $this->convertValue($values[self::REGEX_CAPTURE_VALUE][$currentIndex]);
            }
        }
    }

    /**
     * Converts the value to appropriate type.
     * @param string $value
     * @return mixed
     */
    private function convertValue($value) {
        $value = trim($value, "\"'");
        if (preg_match(self::REGEX_ARRAY, $value) == 0) {
            return $this->transformScalar($value);
        }

        $valuesString = trim($value, "[]");
        $valuesRegex = sprintf(self::REGEX_VALUE, self::REGEX_CAPTURE_PARAM, self::REGEX_CAPTURE_VALUE);
        return $this->getParameterValues($valuesString, $valuesRegex);
    }

    /**
     * Transforms the string to appropriate scalar.
     * @param string $value
     * @return string|float|integer|boolean transformed value
     */
    private function transformScalar($value) {
        $this->transformIfIsNumeric($value);
        $this->transformIfIsBoolean($value);
        return $value;
    }

    /**
     * Transform value if is numeric.
     * @param string $value
     * @return void
     */
    private function transformIfIsNumeric(&$value) {
        if (is_numeric($value)) {
            $value = strpos($value, ".") ? floatval($value) : intval($value);
        }
    }

    /**
     * Transform value if is boolean.
     * @param string $value
     * @return void
     */
    private function transformIfIsBoolean(&$value) {
        if ($value === "true" || $value === "false") {
            $value = $value === "true";
        }
    }

    /**
     * Converts annotationList into an array of annotation objects.
     * @param integer $target
     * @param string $targetLocation
     * @param array $annotationList
     * @return \Brickoo\Component\Annotation\Annotation[]
     */
    private function convertAnnotations($target, $targetLocation, array $annotationList) {
        $annotations = [];
        foreach ($annotationList as $annotation) {
            $annotations[] = new Annotation($target, $targetLocation, $annotation["name"], $annotation["values"]);
        }
        return $annotations;
    }

}
