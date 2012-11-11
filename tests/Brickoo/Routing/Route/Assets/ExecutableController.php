<?php

    namespace Tests\Brickoo\Routing\Route\Assets;

    class ExecutableController {

        public function returnValues(\Brickoo\Routing\Route\Interfaces\Executable $Route) {
            return $Route->getParameter('param1') ." & ". $Route->getParameter('param2');
        }

    }