<?php

$collection = new \Brickoo\Routing\RouteCollection("routes", "/");
$collection->addRoutes([
    new \Brickoo\Routing\Route\GenericRoute("test.route.2", "/", "SomeController", "someAction")
]);
return $collection;