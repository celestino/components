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

namespace Brickoo\Validation\Constraint;

use Brickoo\Validation\Constraint,
    Brickoo\Validation\Argument;

/**
 * ContainsInstancesOfConstraint
 *
 * Constraint to assert that an array or traversable
 * contains just values of the expected instance type.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class ContainsInstancesOfConstraint implements Constraint {

    /** @var string */
    private $expectedInstanceOf;

    /**
     * Class constructor.
     * @param string $expectedType the values expected type
     * @throws \InvalidArgumentException if an argument is not valid.
     * @return void
     */
    public function __construct($expectedInstanceType) {
        Argument::IsString($expectedInstanceType);
        $this->expectedInstanceOf = $expectedInstanceType;
    }

    /**
     * {@inheritDoc}
     * @param array|\Traversable $traversable
     */
    public function matches($traversable) {
        Argument::IsTraversable($traversable);

        $result = true;
        foreach ($traversable as $value) {
            if (! $value instanceof $this->expectedInstanceOf) {
                $result = false;
                break;
            }
        }
        return $result;
    }

}