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

namespace Brickoo\Component\Http\Exception;

use Brickoo\Component\Http\Exception;

/**
 * UnableToMoveFormFileException
 *
 * Exception thrown if the form file could not be moved to an other location.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class UnableToMoveFormFileException extends Exception {

    /**
     * Class constructor.
     * Calls the parent Exception constructor.
     * @param string $uploadFilePath
     * @param string $targetFilePath
     * @param null|\Exception $previousException
     */
    public function __construct($uploadFilePath, $targetFilePath, \Exception $previousException = null) {
        parent::__construct(
            sprintf("The form file `%s` could not be move to `%s`.", $uploadFilePath, $targetFilePath),
            0, $previousException
        );
    }

}
