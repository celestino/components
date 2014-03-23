<?php

use Brickoo\Component\Annotation\Definition,
    Brickoo\Component\Annotation\DefinitionCollection,
    Brickoo\Component\Annotation\Definition\AnnotationDefinition,
    Brickoo\Component\Annotation\Definition\ParameterDefinition,
    Brickoo\Component\Annotation\Definition\TargetDefinition;

/**
 * Definition annotations:
 * @Controller (path = "/")
 * @Route (path = "/list")
 * @Assert (maxlength = 30)
 */

$definition = new Definition("definition.name");

$collection = new DefinitionCollection(new TargetDefinition(TargetDefinition::TYPE_CLASS));
$annotation = new AnnotationDefinition("Controller", true);
$annotation->addParameter(new ParameterDefinition("path", "string", true));
$collection->push($annotation);
$definition->addCollection($collection);


$collection = new DefinitionCollection(new TargetDefinition(TargetDefinition::TYPE_METHOD));
$annotation = new AnnotationDefinition("Route", true);
$annotation->addParameter(new ParameterDefinition("path", "string", true));
$collection->push($annotation);
$definition->addCollection($collection);

$collection = new DefinitionCollection(new TargetDefinition(TargetDefinition::TYPE_PROPERTY));
$annotation = new AnnotationDefinition("Assert", true);
$annotation->addParameter(new ParameterDefinition("maxlength", "integer", true));
$collection->push($annotation);
$definition->addCollection($collection);

return $definition;