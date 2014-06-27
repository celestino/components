<?php

use Brickoo\Component\Annotation\Annotation,
    Brickoo\Component\Annotation\Definition\DefinitionCollection,
    Brickoo\Component\Annotation\Definition\AnnotationDefinition,
    Brickoo\Component\Annotation\Definition\ParameterDefinition;

/**
 * Definition annotations:
 * @Controller (path = "/")
 * @Route (path = "/list")
 * @Assert (maxlength = 30)
 */

$collection = new DefinitionCollection("definition.name");

$annotation = new AnnotationDefinition("Controller", Annotation::TARGET_CLASS);
$annotation->addParameter(new ParameterDefinition("path", "string"));
$collection->push($annotation);

$annotation = new AnnotationDefinition("Route", Annotation::TARGET_METHOD);
$annotation->addParameter(new ParameterDefinition("path", "string"));
$collection->push($annotation);

$annotation = new AnnotationDefinition("Assert", Annotation::TARGET_PROPERTY);
$annotation->addParameter(new ParameterDefinition("maxlength", "integer"));
$collection->push($annotation);

return $collection;
