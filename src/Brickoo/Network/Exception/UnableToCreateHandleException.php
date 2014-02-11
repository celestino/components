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

namespace Brickoo\Network\Exception;

use Brickoo\Network\Exception;

/**
 * UnableToCreateHandleException
 *
 * Exception throwed if a resource handle could not be created for a network connection.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class UnableToCreateHandleException extends Exception {

    /**ŝ
     * Class constructor.
     * Calls the parent Exception constructor.
     * @param string $socketAdress the socket adress
     * @param integer $errorCode the error code throwed
     * @param string $errorMessage the error message throwed
     * @param \Exception $previousException
     * @return void
     */
    public function __construct($socketAdress, $errorCode, $errorMessage, \Exception $previousException = null) {
        parent::__construct(sprintf(
            "The resource handle for the adress `%s` could not be created. Error: [#%d] %s ",
            $socketAdress, $errorCode, $errorMessage
        ), 0, $previousException);
    }

}