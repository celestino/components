<?php

/*
 * Copyright (c) 2011-2015, Celestino Diaz <celestino.diaz@gmx.de>
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

namespace Brickoo\Component\Annotation;

use Brickoo\Component\Annotation\Exception\FileDoesNotExistException;
use Brickoo\Component\Annotation\Exception\UnableToLocateQualifiedClassNameException;
use Brickoo\Component\Common\Collection;
use Brickoo\Component\Common\Assert;
use ReflectionClass;

/**
 * AnnotationClassFileReader
 *
 * Implements an annotation reader for class files.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class AnnotationClassFileReader {

    /** @var \Brickoo\Component\Annotation\AnnotationReflectionClassReader */
    private $annotationReflectionReader;

    /**
     * Class constructor.
     * @param \Brickoo\Component\Annotation\AnnotationReflectionClassReader $annotationReflectionReader
     */
    public function __construct(AnnotationReflectionClassReader $annotationReflectionReader) {
        $this->annotationReflectionReader = $annotationReflectionReader;
    }

    /**
     *
     * @param \Brickoo\Component\Common\Collection $collection
     * @param string $filename
     * @throws \Brickoo\Component\Annotation\Exception\FileDoesNotExistException
     * @throws \Brickoo\Component\Annotation\Exception\UnableToLocateQualifiedClassNameException
     * @return \Brickoo\Component\Annotation\AnnotationReaderResult
     */
    public function getAnnotations(Collection $collection, $filename) {
        Assert::isString($filename);
        $reflectionClass = $this->getReflectionClass($filename);
        return $this->annotationReflectionReader->getAnnotations($collection, $reflectionClass);
    }

    /**
     * Returns the corresponding reflection class.
     * @param string $filename
     * @throws \Brickoo\Component\Annotation\Exception\UnableToLocateQualifiedClassNameException
     * @return \ReflectionClass
     */
    private function getReflectionClass($filename) {
        $this->checkFileAvailability($filename);

        $fileContent = file_get_contents($filename);
        $qualifiedName = sprintf("%s%s",
            $this->getNamespace($fileContent),
            $this->getClassName($fileContent)
        );


        if (preg_match("~^[\\\\][a-zA-Z_\x7f-\xff][\\\\\\w\x7f-\xff]+$~", $qualifiedName) == 0) {
            throw new UnableToLocateQualifiedClassNameException($filename);
        }

        include $filename;
        return new ReflectionClass($qualifiedName);
    }

    /**
     * Checks if the file exists and is readable.
     * @param string $filename
     * @throws \Brickoo\Component\Annotation\Exception\FileDoesNotExistException
     * @return void
     */
    private function checkFileAvailability($filename) {
        if (! is_readable($filename)) {
            throw new FileDoesNotExistException($filename);
        }
    }

    /**
     * Returns the file namespace.
     * @param string $fileContent
     * @return string the file namespace
     */
    private function getNamespace($fileContent) {
        $matches = null;
        $namespace = "\\";
        preg_match("~namespace\\s+(?<namespace>[a-zA-Z_\x7f-\xff][\\\\\\w\x7f-\xff]+)\\s*[\\;\\{]{1}~i", $fileContent, $matches);
        if (isset($matches["namespace"]) && (! empty($matches["namespace"]))) {
            $namespace .= $matches["namespace"]."\\";
        }
        return $namespace;
    }

    /**
     * Returns the class name.
     * @param string $fileContent
     * @return string the class name
     */
    private function getClassName($fileContent) {
        $matches = null;
        preg_match("~[\r\n][a-z\\s]*class\\s+(?<class>[a-zA-Z_\x7f-\xff][\\w\x7f-\xff]+)[\\w\\s,]*\\s*\\{~i", $fileContent, $matches);
        return isset($matches["class"]) ? trim($matches["class"]) : "";
    }

}
