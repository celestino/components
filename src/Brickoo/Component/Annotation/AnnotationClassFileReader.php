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

namespace Brickoo\Component\Annotation;

use Brickoo\Component\Annotation\AnnotationReflectionClassReader,
    Brickoo\Component\Annotation\Definition,
    Brickoo\Component\Annotation\Exception\FileDoesNotExistException,
    Brickoo\Component\Annotation\Exception\UnableToLocateClassNameException,
    Brickoo\Component\Annotation\Exception\UnableToLocateQualifiedClassNameException,
    Brickoo\Component\Validation\Argument,
    ReflectionClass;

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
     * @param \Brickoo\Component\Annotation\AnnotationReflectionClassReader $annotationParser
     */
    public function __construct(AnnotationReflectionClassReader $annotationReflectionReader) {
        $this->annotationReflectionReader = $annotationReflectionReader;
    }

    /**
     *
     * @param \Brickoo\Component\Annotation\Definition $definition
     * @param string $filename
     * @throws \Brickoo\Annotation\Exception\FileDoesNotExistException
     * @throws \Brickoo\Annotation\Exception\UnableToLocateQualifiedClassNameException
     * @throws \Brickoo\Annotation\Exception\UnableToLocateClassNameException
     * @return \Brickoo\Component\Annotation\AnnotationReaderResult
     */
    public function getAnnotations(Definition $definition, $filename) {
        Argument::IsString($filename);
        $reflectionClass = $this->getReflectionClass($filename);
        return $this->annotationReflectionReader->getAnnotations($definition, $reflectionClass);
    }

    /**
     * Returns the corresponding reflection class.
     * @param string $filename
     * @throws \Brickoo\Annotation\Exception\UnableToLocateQualifiedClassNameException
     * @return \ReflectionClass
     */
    private function getReflectionClass($filename) {
        $this->checkFileAvailability($filename);

        $fileContent = file_get_contents($filename);
        $qualifiedName = sprintf("%s%s",
            $this->getNamespace($fileContent),
            $this->getClassName($fileContent)
        );


        if (preg_match("~^[\\\\][\\\\\w]+$~", $qualifiedName) == 0) {
            throw new UnableToLocateQualifiedClassNameException($filename);
        }

        include $filename;
        return new ReflectionClass($qualifiedName);
    }

    /**
     * Checks if the file exists and is readable.
     * @param string $filename
     * @throws \Brickoo\Annotation\Exception\FileDoesNotExistException
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
        preg_match("~namespace\s+(?<namespace>[a-zA-Z_\x7f-\xff][\\\\\w\x7f-\xff]+)\s*\;~i", $fileContent, $matches);
        if (isset($matches["namespace"]) && (! empty($matches["namespace"]))) {
            $namespace .= $matches["namespace"]."\\";
        }
        return $namespace;
    }

    /**
     * Returns the class name.
     * @param string $fileContent
     * @throws \Brickoo\Annotation\Exception\UnableToLocateClassNameException
     * @return string the class name
     */
    private function getClassName($fileContent) {
        $matches = null;
        preg_match("~[\r\n][a-z\s]*class\s+(?<class>[a-zA-Z_\x7f-\xff][\w\x7f-\xff]+)[\w\s,]*\s*\{~i", $fileContent, $matches);
        return isset($matches["class"]) ? trim($matches["class"]) : "";
    }

}