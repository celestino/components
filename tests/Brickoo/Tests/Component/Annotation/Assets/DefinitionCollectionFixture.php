<?php

use Brickoo\Component\Annotation\Definition\DefinitionCollection,
    Brickoo\Component\Annotation\Definition\AnnotationDefinition,
    Brickoo\Component\Annotation\Definition\ParameterDefinition;

/**
 * Definition annotations:
 * @Controller (path = "/")
 * @Route (path = "/list")
 * @Assert (maxlength = 30)
 */

$collection = new DefinitionCollection("definition.name");

$annotation = new AnnotationDefinition("Controller");
$annotation->addParameter(new ParameterDefinition("path", "string"));
$collection->push($annotation);

$annotation = new AnnotationDefinition("Route");
$annotation->addParameter(new ParameterDefinition("path", "string"));
$collection->push($annotation);

$annotation = new AnnotationDefinition("Assert");
$annotation->addParameter(new ParameterDefinition("maxlength", "integer"));
$collection->push($annotation);

return $collection;
