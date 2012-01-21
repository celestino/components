<?php

    use Brickoo\Library\Routing\RouteCollection;

    $RouteCollection = new RouteCollection();

    $RouteCollection->getRoute()
                    ->setController('\module\lib\Controller::method')
                    ->setPath('/')
                    ->setMethod('GET');

    return $RouteCollection;

?>