<?php

use Brickoo\Component\Annotation\Annotation,
    Brickoo\Component\Annotation\AnnotationCollection,
    Brickoo\Component\Annotation\AnnotationClassReaderResult,
    Brickoo\Component\Annotation\AnnotationTarget,
    Brickoo\Component\Annotation\AnnotationTargetTypes;

/**
 * Annotations:
 * @Controller (path = "/")
 * @Route (path = "/list")
 * @Assert (maxlength = 30)
 */

$readerResult = new AnnotationClassReaderResult("definition.name", "\\Some\\Class");

$annotationCollection = new AnnotationCollection(new AnnotationTarget(AnnotationTargetTypes::TYPE_CLASS, "AnnotatedClass"));
$annotationCollection->push(new Annotation("Controller", ["path" => "/"]));
$readerResult->addCollection($annotationCollection);

$annotationCollection = new AnnotationCollection(new AnnotationTarget(AnnotationTargetTypes::TYPE_METHOD, "AnnotatedClass", "listAction"));
$annotationCollection->push(new Annotation("Route", ["path" => "/list"]));
$readerResult->addCollection($annotationCollection);

$annotationCollection = new AnnotationCollection(new AnnotationTarget(AnnotationTargetTypes::TYPE_PROPERTY, "AnnotatedClass", "property"));
$annotationCollection->push(new Annotation("Assert", ["maxlength" => 30]));
$readerResult->addCollection($annotationCollection);

return $readerResult;