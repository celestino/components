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

    namespace Brickoo\Http\Request\Factory;

    use Brickoo\Validator\Argument;

    /**
     * Form
     *
     * Describes a factory for a http request form parameters object.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class Form {

        /**
         * Creates a request form parameters objectcontaining the global POST values.
         * @return \Brickoo\Http\Request\Form
         */
        public static function Create() {
            return new \Brickoo\Http\Request\Form($_POST);
        }

        /**
         * Creates a request form parameters object from the extracted values.
         * @param strin $messageBody the message body string to extract the values from
         * @throws \InvalidArgumentException if the argument is not valid
         * @return \Brickoo\Http\Request\Form
         */
        public static function CreateFromString($messageBody) {
            Argument::IsString($messageBody);

            parse_str(rawurldecode($messageBody), $importedFormParameters);
            return new \Brickoo\Http\Request\Form($importedFormParameters);
        }

    }