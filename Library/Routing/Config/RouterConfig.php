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

    namespace Brickoo\Library\Routing\Config;

    use Brickoo\Library\Config;
    use Brickoo\Library\Routing\Interfaces;
    use Brickoo\Library\Validator\TypeValidator;

    /**
     * RouterConfig
     *
     * Implements the configuration for a Router class.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class RouterConfig implements Interfaces\RouterConfigInterface
    {

        /**
         * Holds the configuration for a Router class.
         * @var array
         */
        protected $configuration;

        /**
         * Returns the configuration.
         * @return array the configuration
         */
        public function getConfiguration()
        {
            return $this->configuration;
        }

        /**
         * Sets the configuration to use.
         * @param array $configuration the configuration to use
         * @return \Brickoo\Library\Routing\Config\RouterConfig
         */
        public function setConfiguration(array $configuration)
        {
            TypeValidator::ArrayContainsKeys(array('cacheDirectory', 'modules'), $configuration);
            TypeValidator::IsArray($configuration['modules']);

            $this->configuration = $configuration;

            return $this;
        }

        /**
         * Configures the Router instance using the available configuration.
         * @param \Brickoo\Library\Routing\Interfaces\RouterInterface $Routerthe Router to configure
         * @throws Config\Exceptions\ConfigurationMissingException if the configuration is missing
         * @return \Brickoo\Library\Routing\Config\RouterConfig
         */
        public function configure(\Brickoo\Library\Routing\Interfaces\RouterInterface $Router)
        {
            if (empty($this->configuration))
            {
                throw new Config\Exceptions\ConfigurationMissingException('RouterConfig');
            }

            $Router->setCacheDirectory($this->configuration['cacheDirectory']);
            $Router->setModules($this->configuration['modules']);

            return $this;
        }

    }

?>