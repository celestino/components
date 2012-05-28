<?php

    namespace Tests\Brickoo\Http\Form\Fixture;

    class SimpleFormFixture extends \Brickoo\Http\Form\SimpleForm {

        public function setErrors() {
            $this->errors = 1;
        }

    }