<?php

$collection = new \Brickoo\Component\Routing\Route\RouteCollection("routes", "/");
$collection->addRoutes([
    new \Brickoo\Component\Routing\Route\GenericRoute("test.route.1", "/", "SomeController", "someAction")
]);
return $collection;