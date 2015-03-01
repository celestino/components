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

namespace Brickoo\Component\Validation\Constraint;

use Brickoo\Component\Common\Assert;

/**
 * LengthConstraint
 *
 * Constraint to assert that the string value length
 * is between a min and max length.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class LengthConstraint implements Constraint {

    /** @var integer */
    private $minLength;

    /** @var integer */
    private $maxLength;

    /**
     * Class constructor.
     * @param integer $minLength
     * @param null|Integer $maxLength
     */
    public function __construct($minLength, $maxLength = null) {
        Assert::isInteger($minLength);
        Assert::isInteger($maxLength);
        $this->minLength = $minLength;
        $this->maxLength = $maxLength;
    }

    /** {@inheritDoc} */
    public function matches($value) {
        Assert::isString($value);
        $valueLength = strlen($value);

        return ($valueLength >= $this->minLength
            && ($this->maxLength === null || $valueLength <= $this->maxLength)
        );
    }

}
