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

namespace Brickoo\Component\Routing\Collector;

use ArrayIterator,
    DirectoryIterator,
    InvalidArgumentException,
    RecursiveDirectoryIterator,
    RecursiveIteratorIterator,
    RegexIterator,
    Brickoo\Component\Routing\RouteCollection,
    Brickoo\Component\Routing\RouteCollector,
    Brickoo\Component\Validation\Argument;

/**
 * FileCollector
 *
 * Implements a route collector based on one or many files
 * which have to return a route collection.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class FileRouteCollector implements RouteCollector {

    /** @var string */
    private $routingPath;

    /** @var string */
    private $routingFilename;

    /** @var boolean */
    private $searchRecursively;

    /** @var array */
    private $collections;

    /**
     * Class constructor.
     * @param string $routingDirectory the routing directory
     * @param string $routingFilename the route filename to look for
     * @param boolean $searchRecursively flag to search recursively
     * @throws \InvalidArgumentException if an argument is not valid
     * @return void
     */
    public function __construct($routingPath, $routingFilename, $searchRecursively = false) {
        Argument::IsString($routingPath);
        Argument::IsString($routingFilename);
        Argument::IsBoolean($searchRecursively);

        if (empty($routingPath)) {
            throw new InvalidArgumentException("The routing path cannot be empty.");
        }

        if (empty($routingFilename)) {
            throw new InvalidArgumentException("The routing filename cannot be empty.");
        }

        $this->routingPath = $routingPath;
        $this->routingFilename = $routingFilename;
        $this->searchRecursively = $searchRecursively;
        $this->collections = [];
    }

    /** {@inheritDoc} */
    public function collect() {
        $filePaths = $this->searchRecursively ? $this->getRecursiveFilePaths() : $this->getFilePaths();

        foreach ($filePaths as $filePath) {
            if (($RouteCollection = include $filePath) && $RouteCollection instanceof RouteCollection) {
                $this->collections[] = $RouteCollection;
            }
        }
        return $this;
    }

    /**
     * {@inheritDoc}
     * @see IteratorAggregate::getIterator()
     * @return \ArrayIterator containing the route collections
     */
    public function getIterator() {
        return new ArrayIterator($this->collections);
    }

    /**
     * Returns the file paths of the available route collections.
     * @return array the matching file paths
     */
    private function getFilePaths() {
        $collectedFilePaths = [];

        foreach (new DirectoryIterator($this->routingPath) as $SplFileInfo) {
            if ((! $SplFileInfo->isDot())
                && (! $SplFileInfo->isDir())
                && (strpos($SplFileInfo->getFilename(), $this->routingFilename) !== false)
            ){
                $collectedFilePaths[] = $SplFileInfo->getRealPath();
            }
        }

        return $collectedFilePaths;
    }

    /**
     * Returns the file paths of the route collections recurservely collected.
     * @return array the available file paths
     */
    private function getRecursiveFilePaths() {
        $collectedFilePaths = [];

        $Directory = new RecursiveDirectoryIterator($this->routingPath);
        $Iterator = new RecursiveIteratorIterator($Directory);
        foreach (new RegexIterator($Iterator, "/^.*". $this->routingFilename ."$/i", RegexIterator::MATCH) as $SplFileInfo) {
            $collectedFilePaths[] = $SplFileInfo->getRealPath();
        }

        return $collectedFilePaths;
    }

}