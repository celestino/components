<?php

return new \Brickoo\Routing\Route\RouteCollection("routes", "/", array(
    new \Brickoo\Routing\Route\Route("test.route.1", "/", "SomeController", "someAction")
));