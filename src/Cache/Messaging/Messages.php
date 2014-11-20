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

namespace Brickoo\Component\Cache\Messaging;

/**
 * Messages
 *
 * Defines the cache messages.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class Messages {

    /**
     * Asks for a cached content.
     * @var string
     */
    const GET = "brickoo.message.cache.get";

    /**
     * Notifies that the content has to be cached.
     * @var string
     */
    const SET = "brickoo.message.cache.set";

    /**
     * Asks for a cached content otherwise a callback should be executed.
     * @var string
     */
    const CALLBACK = "brickoo.message.cache.callback";

    /**
     * Notifies that some cached content has to be deleted.
     * @var string
     */
    const DELETE = "brickoo.message.cache.delete";

    /**
     * Notifies that all cached content has to be flushed.
     * @var string
     */
    const FLUSH = "brickoo.message.cache.flush";

}
