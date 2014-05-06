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
namespace Brickoo\Component\IoC\Definition;

use Brickoo\Component\Validation\Argument;

/**
 * ArgumentDefinition
 *
 * Implements a parameter definition.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class ArgumentDefinition {

    /** @var string */
    private $name;

    /** @var mixed */
    private $value;

    /**
     * Class constructor.
     * @param mixed $value
     * @param string $name
     */
    public function __construct($value, $name = "") {
        Argument::IsString($name);
        $this->name = $name;
        $this->value = $value;
    }

    /**
     * Checks if the argument has a name.
     * @return boolean check result
     */
    public function hasName() {
        return (! empty($this->name));
    }

    /**
     * Returns the parameter name.
     * @return string the parameter name
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Returns the parameter value.
     * @return mixed the parameter value
     */
    public function getValue() {
        return $this->value;
    }

    /**
     * Sets the parameter value.
     * @param mixed $value
     * @return \Brickoo\Component\IoC\Definition\ArgumentDefinition
     */
    public function setValue($value) {
        $this->value = $value;
        return $this;
    }

}
