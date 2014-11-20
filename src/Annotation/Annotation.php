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

namespace Brickoo\Component\Annotation;

use Brickoo\Component\Validation\Argument;

/**
 * Annotation
 *
 * Implements an annotation containing the target, name and values.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class Annotation  {

    /** @const annotation targets */
    const TARGET_CLASS = 1;
    const TARGET_METHOD = 2;
    const TARGET_PROPERTY = 4;

    /** @var integer */
    protected $target;

    /** @var string */
    protected $targetLocation;

    /** @var string */
    protected $name;

    /** @var array */
    protected $values;

    /**
     * Class constructor.
     * @param integer $target
     * @param string $targetLocation
     * @param string $name the annotation name
     * @param array $values the annotation values
     */
    public function __construct($target, $targetLocation, $name, array $values = []) {
        Argument::isInteger($target);
        Argument::isString($targetLocation);
        Argument::isString($name);
        $this->target = $target;
        $this->targetLocation = $targetLocation;
        $this->name = $name;
        $this->values = $values;
    }

    /**
     * Returns the annotation target
     * @return integer the annotation target
     */
    public function getTarget() {
        return $this->target;
    }

    /**
     * Returns the target location.
     * @return string the target location
     */
    public function getTargetLocation() {
        return $this->targetLocation;
    }

    /**
     * Returns the annotation name.
     * @return string annotation name
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Returns the annotation values.
     * @return array annotations values
     */
    public function getValues() {
        return $this->values;
    }

    /**
     * Checks if the annotation has a value.
     * @return boolean check result
     */
    public function hasValues() {
        return (! empty($this->values));
    }

}
