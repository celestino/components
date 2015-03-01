<?php

$collection = new \Brickoo\Component\Routing\Route\RouteCollection("route.collection.1", "/");
$collection->addRoutes([
    new \Brickoo\Component\Routing\Route\GenericRoute("test.route.1", "/", "SomeController", "someAction")
]);
return $collection;
