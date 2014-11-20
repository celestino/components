<?php

/*
 * Copyright (c) 2011-2014, Celestino Diaz <celestino.diaz@gmx.de>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace Brickoo\Component\Validation\Constraint;

use Brickoo\Component\Validation\Argument;

/**
 * TraversableContainsKeys
 *
 * Constraint to assert that an array or traversable contains expected keys.
 * Does not have any effect if the traversable also contains other keys.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class ContainsKeysConstraint implements Constraint {

    /** @var array */
    private $expectedKeys;

    /**
     * Class constructor.
     * @param array $expectedKeys the expected keys
     */
    public function __construct(array $expectedKeys) {
        $this->expectedKeys = $expectedKeys;
    }


    /**
     * {@inheritDoc}
     * @param array|\Traversable $traversable
     */
    public function matches($traversable) {
        Argument::isTraversable($traversable);

        $result = array_diff($this->expectedKeys, $this->getTraversableKeys($traversable));
        return empty($result);
    }

    /**
     * Returns the traversable contained keys as values.
     * @param array|\Traversable $traversable the traversable to return the keys from
     * @return array the array keys as values
     */
    private function getTraversableKeys($traversable) {
        if (is_array($traversable)) {
            return array_keys($traversable);
        }
        return array_keys(iterator_to_array($traversable, true));
    }

}
