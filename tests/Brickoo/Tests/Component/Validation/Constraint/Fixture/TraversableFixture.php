<?php

    namespace Brickoo\Tests\Component\Validation\Constraint\Fixture;

    class TraversableFixture implements \IteratorAggregate {

        public $key1 = "value1";
        public $key2 = "value2";
        public $key3 = "value3";

        public function getIterator() {
            return new \ArrayIterator($this);
        }

    }