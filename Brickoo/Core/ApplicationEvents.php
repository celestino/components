<?php

    /*
     * Copyright (c) 2011-2012, Celestino Diaz <celestino.diaz@gmx.de>.
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
     * 3. Neither the name of Brickoo nor the names of its contributors may be used
     *    to endorse or promote products derived from this software without specific
     *    prior written permission.
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

    namespace Brickoo\Core;

    /**
     * ApplicationEvents
     *
     * Holds the core application events.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class ApplicationEvents
    {

        /**
        * Notifies the application boot event.
        * @var string
        */
        const EVENT_BOOT = 'application.boot';

        /**
         * Notifies that an error/exception occured while running the application.
         * @var string
         */
        const EVENT_ERROR = 'application.error';

        /**
         * Notifies that the application has finished and can be shutdown.
         * @var string
         */
        const EVENT_SHUTDOWN = 'application.shutdown';

        /**
         * Notifies that the module route requires session management and it could be configured.
         * @var string
         */
        const EVENT_SESSION_CONFIGURE = 'session.configure';

        /**
         * Asks for a cached response if the module route did enable the response cache.
         * @var string
         */
        const EVENT_RESPONSE_LOAD = 'response.load';

        /**
         * Asks for a fresh response if the cached response has not been returned or did not be
         * enabled by the module route.
         * @var string
         */
        const EVENT_RESPONSE_GET = 'response.get';

        /**
         * Notifies that the response could be cached if the module route did enabled response caching.
         * @var string
         */
        const EVENT_RESPONSE_SAVE = 'response.save';

        /**
         * Notifies that the response could be sent now.
         * @var string
         */
        const EVENT_RESPONSE_SEND = 'response.send';

        /**
         * Notifies that the application did not get a fresh response after asking for.
         * @var string
         */
        const EVENT_RESPONSE_MISSING = 'response.missing';

    }