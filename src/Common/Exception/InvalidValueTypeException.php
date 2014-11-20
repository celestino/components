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

namespace Brickoo\Component\Common\Exception;

use Brickoo\Component\Common\Exception;

/**
 * InvalidValueTypeException
 *
 * Exception thrown f trying to add a value with an invalid type.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class InvalidValueTypeException extends Exception {

    /**
     * Class constructor.
     * Calls the parent Exception constructor.
     * @param mixed $value
     * @param null|\Exception $previousException
     */
    public function __construct($value, \Exception $previousException = null) {
        $type = (is_object($value) ? get_class($value) : gettype($value));
        parent::__construct(sprintf("The value of type `%s` is invalid.", $type), 0, $previousException);
    }

}
