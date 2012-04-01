<?php

    /**
     * Test controller for the testGetResponse case.
     */
    class TestController
    {
        public function testMethod()
        {
            return 'test response.';
        }

        public function exceptionMethod()
        {
            throw new \Brickoo\Http\Exceptions\ResponseTemplateNotAvailableException();
        }
    }