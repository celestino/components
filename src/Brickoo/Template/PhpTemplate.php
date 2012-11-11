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

    namespace Brickoo\Template;

    use Brickoo\Validator\Argument;

    /**
     * PhpTemplate
     *
     * Implements a PHP based template to generate a response message body.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class PhpTemplate implements Interfaces\Template {

        /** @var string */
        protected $templateFile;

        /** @var array */
        protected $templateVars;

        /**
         * Class constructor.
         * @param string $templateFile the php template to use
         * @param array $templateVars the template variables to make accessible
         * @throws \InvalidArgumentException if an argument is not valid
         * @return void
         */
        public function __construct($templateFile, array $templateVars = array()) {
            Argument::IsString($templateFile);
            $this->templateFile = $templateFile;
            $this->templateVars = $templateVars;
        }

        /** {@inheritDoc} */
        public function render() {
            try {
                extract($this->templateVars, EXTR_SKIP);
                $TPL_DIR = $this->getTemplateDirectory();

                ob_start();
                require ($this->templateFile);
                $output = ob_get_contents();
                ob_end_clean();
            }
            catch (\Exception $Exception) {
                throw new Exceptions\RenderingAborted($Exception);
            }

            return $output;
        }

        /**
         * Returns the absolute directory path of the current template.
         * @return string the template directory path
         */
        private function getTemplateDirectory() {
            return realpath(dirname($this->templateFile)) . DIRECTORY_SEPARATOR;
        }

    }