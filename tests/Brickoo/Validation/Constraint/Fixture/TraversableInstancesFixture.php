<?php

    namespace Brickoo\Tests\Validation\Constraint\Fixture;

    class TraversableInstancesFixture implements \IteratorAggregate {

        public $instance1 = null;
        public $instance2 = null;
        public $instance3 = null;

        public function __construct() {
            $this->instance1 = new \ArrayObject();
            $this->instance2 = new \ArrayObject();
            $this->instance3 = new \ArrayObject();
        }

        public function getIterator() {
            return new \ArrayIterator($this);
        }

    }