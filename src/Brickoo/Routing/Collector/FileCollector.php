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

    namespace Brickoo\Routing\Collector;

    use Brickoo\Validator\Argument;

    /**
     * FileCollector
     *
     * Implements a route collector base on files which must return a route collection.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class FileCollector implements Interfaces\Collector {

        /** @var string */
        private $routingPath;

        /** @var string */
        private $routingFileName;

        /** @var boolean */
        private $searchRecursively;

        /**
         * Class constructor.
         * @param string $routingDirectory the routing directory
         * @param string $routingFilename the route filename to look for
         * @param boolean $collectRecursively flag to search recursively
         * @throws \InvalidArgumentException if an argument is not valid
         * @throws \UnexpectedValueException if the routing path cannot be opened
         * @return void
         */
        public function __construct($routingPath, $routingFilename, $searchRecursively = false) {
            Argument::IsString($routingPath);
            Argument::IsString($routingFilename);
            Argument::IsBoolean($searchRecursively);

            if (empty($routingPath)) {
                throw new \InvalidArgumentException("The routing path cannot be empty.");
            }

            if (empty($routingFilename)) {
                throw new \InvalidArgumentException("The routing filename cannot be empty.");
            }

            $this->routingPath = $routingPath;
            $this->routingFileName = $routingFilename;
            $this->searchRecursively = $searchRecursively;
        }

        /** {@inheritDoc} */
        public function collect() {
            $collected = $this->getRouteCollection();

            if ($collected === null) {
                throw new Exceptions\RoutesNotAvailable();
            }

            if (! $collected instanceof \Brickoo\Routing\Route\Interfaces\Collection) {
                throw new Exceptions\RouteCollectionExpected($collected);
            }

            return $collected;
        }

        private function getRouteCollection() {
            $collected = null;

            foreach (new \DirectoryIterator($this->routingPath) as $SplFileInfo) {
                if (! $SplFileInfo->isDot()) {
                    if ($SplFileInfo->isDir() && $this->searchRecursively)) {
                }
                }
            }

            return $collected;
        }

    }