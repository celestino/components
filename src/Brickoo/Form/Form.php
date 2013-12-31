<?php

/*
 * Copyright (c) 2011-2013, Celestino Diaz <celestino.diaz@gmx.de>.
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

namespace Brickoo\Form;

use InvalidArgumentException,
    Brickoo\Form\Field,
    Brickoo\Form\Exception\FieldAlreadyExistsException,
    Brickoo\Form\Exception\FieldNotFoundException,
    Brickoo\Validation\Argument,
    Brickoo\Validation\Constraint\ContainsInstancesOfConstraint;

/**
 * Form
 *
 * Implements a form for auto validation of fields.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class Form {

    /** @var string */
    private $actionUrl;

    /** @var string */
    private $formMethod;

    /** @var array instancesOf Brickoo\Form\Field */
    private $fields;

    /** @var array */
    private $invalidFields;

    /**
     * Class constructor.
     * @param string $actionUrl
     * @param string $formMethod
     * @throws \InvalidArgumentException
     * @return void
     */
    public function __construct($actionUrl, $formMethod) {
        Argument::IsString($actionUrl);
        Argument::IsString($formMethod);

        $this->actionUrl = $actionUrl;
        $this->formMethod = $formMethod;
        $this->fields = [];
        $this->invalidFields = [];
    }

    /**
     * Adds Fields to the form.
     * @param array|Traversable $fields constaining instancesOf Field
     * @throws \InvalidArgumentException
     * @return \Brickoo\Form\Form
     */
    public function addFields($fields) {
        Argument::IsTraversable($fields);

        if (! (new ContainsInstancesOfConstraint("\\Brickoo\\Form\\Field"))->matches($fields)) {
            throw new InvalidArgumentException("The argument must contain only instances of \\Brickoo\\Form\\Field.");
        }

        foreach ($fields as $field) {
            $this->addField($field);
        }
        return $this;
    }

    /**
     * Adds a field to the form.
     * @param \Brickoo\Form\Field $field
     * @throws \InvalidArgumentException
     * @throws \Brickoo\Form\Exception\FieldAlreadyExistsException
     * @return \Brickoo\Form\Form
     */
    public function addField(Field $field) {
        if ($this->hasField(($fieldName = $field->getName()))) {
            throw new FieldAlreadyExistsException($fieldName);
        }
        $this->fields[$fieldName] = $field;
        return $this;
    }

    /**
     * Returns a field by its name.
     * @param string $fieldName
     * @throws \InvalidArgumentException
     * @throws \Brickoo\Form\Exception\FieldNotFoundException
     * @return \Brickoo\Form\Field
     */
    public function getField($fieldName) {
        Argument::IsString($fieldName);
        if (! $this->hasField($fieldName)) {
            throw new FieldNotFoundException($fieldName);
        }
        return $this->fields[$fieldName];
    }

    /**
     * Retruns the form fields.
     * @return array containing field instances
     */
    public function getFields() {
        return $this->fields;
    }

    /**
     * Checks if a field with the name exist.
     * @param string $fieldName
     * @throws \InvalidArgumentException
     * @return boolean check result
     */
    public function hasField($fieldName) {
        Argument::IsString($fieldName);
        return isset($this->fields[$fieldName]);
    }

    /**
     * Returns the value(s) of an field.
     * @param string $fieldName
     * @throws FieldNotFoundException
     * @return mixed the values(s) of a field
     */
    public function getFieldValue($fieldName) {
        if (! $this->hasField($fieldName)) {
            throw new FieldNotFoundException($fieldName);
        }
        return $this->fields[$fieldName]->getValue();
    }

    /**
     * Checks if the formData is valid using the form fields validation.
     * @param array $formData
     * @return boolean check result
     */
    public function isValid(array $formData) {
        if (empty($this->fields)) {
            return true;
        }

        $this->invalidFields = [];
        foreach ($this->fields as $field) {
            $fieldName =  $field->getName();
            if ($field->isRequired() && (! isset($formData[$fieldName]))) {
                $this->invalidFields[] = $fieldName;
                continue;
            }

            if (isset($formData[$fieldName])) {
                $field->setValue($formData[$fieldName]);
            }

            if (! $field->isValid()) {
                $this->invalidFields[] = $fieldName;
            }
        }
        return empty($this->invalidFields);
    }

    /**
     * Returns an array representation of the form.
     * @return array form representation
     */
    public function toArray() {
        return [
            "action" => $this->actionUrl,
            "method" => $this->formMethod,
            "fields" => $this->fields,
            "invalid" => $this->invalidFields
        ];
    }

}