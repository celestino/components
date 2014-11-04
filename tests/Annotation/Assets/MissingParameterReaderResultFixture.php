<?php

use Brickoo\Component\Annotation\Annotation;
use Brickoo\Component\Annotation\AnnotationReaderResult;

/**
 * Annotations:
 * @Controller (path = "/")
 * @Route (path = "/list")
 * @Assert (maxlength = 30)
 */

$readerResult = new AnnotationReaderResult("definition.name", "\\SomeClass");
$readerResult->addAnnotation(new Annotation(Annotation::TARGET_CLASS, "\\SomeClass", "Controller", ["path" => "/"]));
$readerResult->addAnnotation(new Annotation(Annotation::TARGET_METHOD, "\\SomeClass", "Route", ["path" => "/list"]));
$readerResult->addAnnotation(new Annotation(Annotation::TARGET_PROPERTY, "\\SomeClass", "Assert", [])); // maxlength missing

return $readerResult;
