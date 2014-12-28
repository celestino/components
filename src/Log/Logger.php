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

namespace Brickoo\Component\Log;

/**
 * Logger
 *
 * Describes an object to store log messages.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
interface Logger {

    const SEVERITY_EMERGENCY    = 0;
    const SEVERITY_ALERT        = 1;
    const SEVERITY_CRITICAL     = 2;
    const SEVERITY_ERROR        = 3;
    const SEVERITY_WARNING      = 4;
    const SEVERITY_NOTICE       = 5;
    const SEVERITY_INFO         = 6;
    const SEVERITY_DEBUG        = 7;

    /**
     * Sends the log messages using log handler assigned.
     * @param array|string $messages the messages to store
     * @param integer $severity the severity level
     * @throws \InvalidArgumentException if an argument is not valid
     * @return \Brickoo\Component\Log\Logger
     */
    public function log($messages, $severity);

}
