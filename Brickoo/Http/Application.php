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

    namespace Brickoo\Http;

    use Brickoo\Core;
    use Brickoo\Validator\TypeValidator;

    /**
     * Implements methods to handle HTTP requests.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class Application extends Core\Application
    {

        /**
         * Checks if the request is cacheable.
         * @todo Needs to look into the request headers.
         * @return boolean check result
         */
        public function isCacheableRequest()
        {
            return in_array($this->Request()->getMethod(), array('GET', 'HEAD'));
        }

        /**
         * Returns the request cache unique identifier.
         * @todo Find a way to make this more "unique"
         * @return string the cache unique identifier
         */
        public function getCacheUID()
        {
            return md5($this->Request()->getPath()) .'.'. md5($this->Request()->Url()->toString());
        }

        /**
         * Checks if the request has a cached response.
         * @todo Needs other cached inforamtion format to store headers as well
         * @return boolean checks result
         */
        public function hasCachedResponse()
        {
            if ((! $ResponseCacheManager = $this->ResponseCacheManager) ||
                (! $cacheUID = $this->getCacheUID())||
                (! $cachedResponseParts = $ResponseCacheManager->get($cacheUID))
            ){
               return false;
            }

            $Response = new \Brickoo\Http\Response();
            $Response->setContent($cachedResponseParts);
            $this->registerResponse($Response);

            return true;
        }

        /**
         * Caches the Response if it is possible.
         * @todo Needs to look into the Response Headers to verify caching
         * @param \Brickoo\Http\Interfaces\ResponseInterface $Response
         * @param \Brickoo\Routing\Interfaces\RequestRouteInterface $RequestRoute
         */
        public function cacheResponse(
            \Brickoo\Http\Interfaces\ResponseInterface $Response,
            \Brickoo\Routing\Interfaces\RequestRouteInterface $RequestRoute
        )
        {
            if((! $this->isCacheableRequest()) || (! $ResponseCacheManager = $this->ResponseCacheManager)) {
                return $this;
            }

            $ResponseCacheManager->set($this->getCacheUID(), $Response->getContent(), 15);

            return $this;
        }

        /**
         * Runs the application.
         * Configures the Router.
         * Calls the Router to get the matching request Route.
         * @return \Brickoo\Http\Application
         */
        public function run()
        {
            try {

                $Router = $this->getRouter();

                $this->configureRouter();

                if ($Router->hasCacheDirectory()) {
                    $Router->loadRoutesFromCache();
                }

                $this->registerRequestRoute(($RequestRoute = $Router->getRequestRoute()));

                if ((! $this->isCacheableRequest()) || (! $this->hasCachedResponse())) {
                    $this->execute();
                }
            }
            catch (\Exception $Exception) {
                $Response = new \Brickoo\Http\Response();
                $Response->setContent($Exception->getMessage());
                $this->registerResponse($Response);
            }

            return $this;
        }

        /**
         * Exectutes the request for a fresh response.
         * @return \Brickoo\Http\Application
         */
        public function execute()
        {
            $RouteController = $this->RequestRoute->getController();
            if (! $RouteController['static']) {
                $RouteController['controller'] = new $RouteController['controller'];
            }

            $Response = call_user_func(array($RouteController['controller'], $RouteController['method']));

            if ($Response instanceof Interfaces\ResponseInterface) {
                $this->registerResponse($Response);
                $this->cacheResponse($Response, $this->RequestRoute);
            }

            return $this;
        }

        /**
         * Sends the Response headers and content.
         * @return \Brickoo\Core\Application
         */
        public function send()
        {
            if (($Response = $this->Response) instanceof Interfaces\ResponseInterface) {
                $Response->send();
            }

            return $this;
        }

    }