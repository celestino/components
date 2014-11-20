<?php

/*
 * Copyright (c) 2011-2014, Celestino Diaz <celestino.diaz@gmx.de>
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

namespace Brickoo\Component\Http;

use Brickoo\Component\Http\Exception\HttpFormFieldNotFoundException;
use Brickoo\Component\Validation\Argument;

/**
 * HttpFormInput
 *
 * Implements a http form inputs container.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class HttpFormInput implements \IteratorAggregate {

    /** @var array */
    private $formFields;

    /** @var array $formFields */
    public function __construct(array $formFields = []) {
        $this->formFields = $formFields;
    }

    /**
     * Check if the form has an input field.
     * @param string $formFieldName
     * @throws \InvalidArgumentException
     * @return boolean check result
     */
    public function hasField($formFieldName) {
        Argument::isString($formFieldName);
        return isset($this->formFields[$formFieldName]);
    }

    /**
     * Check if the form has an input file.
     * @param string $formFileFieldName
     * @return boolean check result
     */
    public function hasFile($formFileFieldName) {
        Argument::isString($formFileFieldName);
        return $this->hasField($formFileFieldName)
            && ($this->formFields[$formFileFieldName] instanceof HttpFormFile);
    }

    /**
     * Return the form file object.
     * @param string $formFileFieldName
     * @throws \Brickoo\Component\Http\Exception\HttpFormFieldNotFoundException
     * @return \Brickoo\Component\Http\HttpFormFile
     */
    public function getFile($formFileFieldName) {
        if (! $this->hasFile($formFileFieldName)) {
            throw new HttpFormFieldNotFoundException($formFileFieldName);
        }
        return $this->getField($formFileFieldName);
    }

    /**
     * Return the form field value.
     * @param string $formFieldName
     * @param null|mixed $defaultValue
     * @return null|mixed the form field value otherwise the default value
     */
    public function getField($formFieldName, $defaultValue = null) {
        if (! $this->hasField($formFieldName)) {
            return $defaultValue;
        }
        return $this->formFields[$formFieldName];
    }

    /**
     * Check if the http form has not fields.
     * @return boolean check result
     */
    public function isEmpty() {
        return  empty($this->formFields);
    }

    /**
     * Extract the form field from the container
     * and return the field value.
     * @param string $formFieldName
     * @throws \Brickoo\Component\Http\Exception\HttpFormFieldNotFoundException
     * @return mixed
     */
    public function extract($formFieldName) {
        if (! $this->hasField($formFieldName)) {
            throw new HttpFormFieldNotFoundException($formFieldName);
        }
        $fieldValue = $this->formFields[$formFieldName];
        unset($this->formFields[$formFieldName]);
        return $fieldValue;
    }

    /**
     * Retrieve an external iterator.
     * @link http://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return \ArrayIterator
     */
    public function getIterator() {
        return new \ArrayIterator($this->formFields);
    }
}
