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

namespace Brickoo\Form;

/**
 * Field
 *
 * Describes a form field.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

interface Field {

    const TYPE_CHECKBOX = "CHECKBOX";
    const TYPE_INPUT_PASSWORD = "INPUT_PASSWORD";
    const TYPE_INPUT_TEXT = "INPUT_TEXT";
    const TYPE_RADIO = "RADIO";
    const TYPE_TEXT = "TEXT";

    /**
     * Returns the field type.
     * @return string the filed type
     */
    public function getType();

    /**
     * Returns the UNIQUE field name.
     * @return string the field name
     */
    public function getName();

    /**
     * Returns the field value(s).
     * @retrun mixed the field value(s)
     */
    public function getValue();

    /**
     * Sets the field value(s).
     * @param mixed the field value(s)
     * @return \Brickoo\Form\Field
     */
    public function setValue($value);

    /**
     * Checks if the field is required.
     * @return boolean check result
     */
    public function isRequired();

    /**
     * Checks if the current field value is valid.
     * @return boolean check result
     */
    public function isValid();

    /**
     * Returns the field view label.
     * @return string the view label
     */
    public function getLabel();

    /**
     * Returns the view error message.
     * @return string the view error message
     */
    public function getErrorMessage();

}