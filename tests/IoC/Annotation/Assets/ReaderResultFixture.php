<?php

use Brickoo\Component\Annotation\Annotation,
    Brickoo\Component\Annotation\AnnotationReaderResult;

/**
 * Annotations:
 * @Dependency ("value")
 * @Dependency ("@dependencyId")
 */

$readerResult = new AnnotationReaderResult("dependency.definition", "\\SomeClass");
$readerResult->addAnnotation(new Annotation(Annotation::TARGET_CLASS, "\\SomeClass", "Dependency", ["value"]));
$readerResult->addAnnotation(new Annotation(Annotation::TARGET_METHOD, "\\SomeClass::someMethod", "Dependency", ["@dependencyId"]));

return $readerResult;
