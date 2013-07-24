<?php

return new \Brickoo\Routing\Route\Collection("routes", "/", array(
    new \Brickoo\Routing\Route\Route("test.route.1", "/", "SomeController", "someAction")
));