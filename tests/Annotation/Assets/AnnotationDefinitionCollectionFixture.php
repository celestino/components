<?php

use Brickoo\Component\Annotation\Annotation;
use Brickoo\Component\Annotation\Definition\AnnotationDefinition;
use Brickoo\Component\Annotation\Definition\AnnotationParameterDefinition;
use Brickoo\Component\Common\Collection;

/**
 * Definition annotations:
 * @Controller (path = "/")
 * @Route (path = "/list")
 * @Assert (maxlength = 30)
 */

$collection = new Collection();

$annotation = new AnnotationDefinition("Controller", Annotation::TARGET_CLASS);
$annotation->addParameter(new AnnotationParameterDefinition("path", "string"));
$collection->add($annotation);

$annotation = new AnnotationDefinition("Route", Annotation::TARGET_METHOD);
$annotation->addParameter(new AnnotationParameterDefinition("path", "string"));
$collection->add($annotation);

$annotation = new AnnotationDefinition("Assert", Annotation::TARGET_PROPERTY);
$annotation->addParameter(new AnnotationParameterDefinition("maxlength", "integer"));
$collection->add($annotation);

return $collection;
