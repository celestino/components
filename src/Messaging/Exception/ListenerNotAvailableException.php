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

namespace Brickoo\Component\Messaging\Exception;

use Brickoo\Component\Messaging\Exception;

/**
 * ListenerNotAvailableException
 *
 * thrown if trying to retrieve an unavailable listener.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class ListenerNotAvailableException extends Exception {

    /**
     * Calls the parent exception constructor.
     * @param string $listenerUID the listener unique identifier not available
     * @param null|\Exception $previousException
     */
    public function __construct($listenerUID, \Exception $previousException = null) {
        parent::__construct(sprintf('The listener with the UID `%s` is not available.', $listenerUID), 0, $previousException);
    }

}
