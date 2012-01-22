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

    namespace Brickoo\Library\Core\Interfaces;

    /**
     * Describes the methods implemented by this interface.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    interface FrontControllerInterface
    {

        /**
         * Lazy initialization of the Brickoo dependency.
         * Returns the Brickoo dependency.
         * @return \Brickoo\Library\Core\Interfaces\BrickooInterface
         */
        public function getBrickoo();

        /**
         * Injects the Brickoo dependency.
         * @param \Brickoo\Library\Core\Interfaces\BrickooInterface $Brickoo the Brickoo dependency
         * @throws Exceptions\DependencyOverwriteException if trying to overwrite the dependency
         * @return \Brickoo\Library\Core\FrontController
         */
        public function injectBrickoo(\Brickoo\Library\Core\Interfaces\BrickooInterface $Brickoo);

        /**
         * Lazy initialization of the Router dependency.
         * Returns the holded Router instance.
         * @return \Brickoo\Library\Routing\Interface\RouterInterface
         */
        public function getRouter();

        /**
         * Injects the Router dependency to use.
         * @param \Brickoo\Library\Routing\Interfaces\RouterInterface $Router the ROuter dependency
         * @throws Exceptions\DependencyOverwriteException if trying to overwrite the dependency
         * @return \Brickoo\Library\Core\FrontController
         */
        public function injectRouter(\Brickoo\Library\Routing\Interfaces\RouterInterface $Router);

        /**
         * Returns the Request dependency.
         * @throws DependencyNotAvailableException if the dependency is not available
         * @return \Brickoo\Library\Core\Interfaces\DynamicrequestInterface
         */
        public function getRequest();

        /**
         * Injects the Request dependency.
         * @param \Brickoo\Library\Core\Interfaces\DynamicrequestInterface $Request the Request dependency
         * @throws Exceptions\DependencyOverwriteException if trying to overwrite the dependency
         * @return \Brickoo\Library\Core\FrontController
         */
        public function injectRequest(\Brickoo\Library\Core\Interfaces\DynamicrequestInterface $Request);

        /**
         * Lazy initialization of the SessionManager dependency.
         * Returns the holded SessionManager instance.
         * @return \Brickoo\Library\Session\Interfaces\SessionManagerInterface
         */
        public function getSessionManager();

        /**
         * Injects a SessionManager dependency.
         * @param \Brickoo\Library\Session\Interfaces\SessionManagerInterface $SessionManager the SessionManager dependency
         * @throws Exceptions\DependencyOverwriteException if trying to overwrite the dependency
         * @return \Brickoo\Library\Core\FrontController
         */
        public function injectSessionManager(\Brickoo\Library\Session\Interfaces\SessionManagerInterface $SessionManager);

        /**
         * Runs the pre configured FrontController.
         * Registers the configuration and objects which should be available.
         * @param integer $environment the environment to use
         * @return void
         */
        public function run($environment);

    }

?>