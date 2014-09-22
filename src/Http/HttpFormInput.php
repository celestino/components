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
