<?php

use Brickoo\Component\Annotation\Annotation,
    Brickoo\Component\Annotation\AnnotationReaderResult;

/**
 * Annotations:
 * @Controller (path = "/")
 * @Route (path = "/list")
 * @Assert (maxlength = 30)
 */

$readerResult = new AnnotationReaderResult("definition.name", "\\SomeClass");
$readerResult->addAnnotation(new Annotation(Annotation::TARGET_CLASS, "\\SomeClass", "Controller", ["path" => "/"]));
$readerResult->addAnnotation(new Annotation(Annotation::TARGET_METHOD, "\\SomeClass::someAction", "Route", ["path" => "/list"]));

// Missing @Assert Annotation

return $readerResult;
