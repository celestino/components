<?php

    namespace Tests\Brickoo\Http\Form\Element\Fixture;

    class GenericFixture extends \Brickoo\Http\Form\Element\Generic {

        public function setErrorMessages(array $messages) {
            $this->errorMessages = $messages;
        }

        public function filter($value){
            $value = parent::filter($value);
            return strtoupper($value);
        }

    }