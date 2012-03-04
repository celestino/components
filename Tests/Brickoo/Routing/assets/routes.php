<?php

    use Brickoo\Routing\RouteCollection;

    $RouteCollection = new RouteCollection();

    $RouteCollection->createRoute('test')
                    ->setController('\module\lib\Controller', 'method', true)
                    ->setPath('/')
                    ->setMethod('GET');

    return $RouteCollection;