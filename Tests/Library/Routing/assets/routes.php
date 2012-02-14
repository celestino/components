<?php

    use Brickoo\Library\Routing\RouteCollection;

    $RouteCollection = new RouteCollection();

    $RouteCollection->getRoute()
                    ->setController('\module\lib\Controller', 'method', true)
                    ->setPath('/')
                    ->setMethod('GET');

    return $RouteCollection;