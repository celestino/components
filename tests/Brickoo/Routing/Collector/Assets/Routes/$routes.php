<?php

return new \Brickoo\Routing\Route\Collection("routes", "/", array(
    new \Brickoo\Routing\Route\Route("test.route.2", "/forum", "SomeController", "someAction")
));