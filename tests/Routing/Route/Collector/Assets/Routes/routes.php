<?php

$collection = new \Brickoo\Component\Routing\Route\RouteCollection("route.collection.2", "/");
$collection->addRoutes([
    new \Brickoo\Component\Routing\Route\GenericRoute("test.route.2", "/", "SomeController", "someAction")
]);
return $collection;
