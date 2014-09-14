<?php

namespace Brickoo\Tests\Component\Validation\Constraint\Fixture;

class TraversableFixture implements \IteratorAggregate {

    public function getIterator() {
        return new \ArrayIterator([
            "key1" => "value1",
            "key2" => "value2",
            "key3" => "value3"
        ]);
    }

}
