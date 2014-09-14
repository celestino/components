<?php

namespace Brickoo\Tests\Component\Validation\Constraint\Fixture;

class TraversableInstancesFixture implements \IteratorAggregate {

    public function getIterator() {
        return new \ArrayIterator([new \ArrayObject(), new \ArrayObject(), new \ArrayObject()]);
    }

}
