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

namespace Brickoo\Component\Annotation\Definition;

use Brickoo\Component\Validation\Argument;

/**
 * ParameterDefinition
 *
 * Implements a parameter definition.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class ParameterDefinition {

    /** @var string */
    private $name;

    /** @var string */
    private $type;

    /** @var boolean */
    private $required;

    /**
     * Class constructor.
     * @param string $name
     * @param string $type
     * @param boolean $required
     * @throws \InvalidArgumentException
     */
    public function __construct($name, $type, $required = true) {
        Argument::IsString($name);
        Argument::IsString($type);
        Argument::IsBoolean($required);
        $this->name = $name;
        $this->type = $type;
        $this->required = $required;
    }

    /**
     * Returns the parameter name.
     * @return string the parameter name
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Returns the parameter type.
     * @return string the parameter type
     */
    public function getType() {
        return $this->type;
    }

    /**
     * Checks if the parameter is required.
     * @return boolean check result
     */
    public function isRequired() {
        return $this->required;
    }

}
