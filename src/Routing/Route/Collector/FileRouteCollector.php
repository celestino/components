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

namespace Brickoo\Component\Routing\Route\Collector;

use ArrayIterator;
use Brickoo\Component\Routing\Route\RouteCollection;
use Brickoo\Component\Validation\Argument;
use DirectoryIterator;
use InvalidArgumentException;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RegexIterator;

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
     * @param string $routingPath the routing directory path
     * @param string $routingFilename the route filename to look for
     * @param boolean $searchRecursively flag to search recursively
     * @throws \InvalidArgumentException if an argument is not valid
     */
    public function __construct($routingPath, $routingFilename, $searchRecursively = false) {
        Argument::isString($routingPath);
        Argument::isString($routingFilename);
        Argument::isBoolean($searchRecursively);

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
        $filePaths = $this->collectRouteCollectionsFilePaths();

        foreach ($filePaths as $filePath) {
            if (($routeCollection = include $filePath) && $routeCollection instanceof RouteCollection) {
                $this->collections[] = $routeCollection;
            }
        }
        return $this->getIterator();
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
     * Return the collected route collections file paths.
     * @return array the collected collection file paths
     */
    private function collectRouteCollectionsFilePaths() {
        return $this->searchRecursively ? $this->getRecursiveFilePaths() : $this->getFilePaths();
    }

    /**
     * Returns the file paths of the available route collections.
     * @return array the matching file paths
     */
    private function getFilePaths() {
        $collectedFilePaths = [];

        foreach (new DirectoryIterator($this->routingPath) as $splFileInfo) {
            if ($splFileInfo->isFile()
                && (strpos($splFileInfo->getFilename(), $this->routingFilename) !== false)) {
                    $collectedFilePaths[] = $splFileInfo->getRealPath();
            }
        }

        return $collectedFilePaths;
    }

    /**
     * Returns the file paths of the route collections recursively collected.
     * @return array the available file paths
     */
    private function getRecursiveFilePaths() {
        $collectedFilePaths = [];

        $directory = new RecursiveDirectoryIterator($this->routingPath);
        $iterator = new RecursiveIteratorIterator($directory);
        foreach (new RegexIterator($iterator, "/^.*".$this->routingFilename."$/i", RegexIterator::MATCH) as $splFileInfo) {
            $collectedFilePaths[] = $splFileInfo->getRealPath();
        }

        return $collectedFilePaths;
    }

}
