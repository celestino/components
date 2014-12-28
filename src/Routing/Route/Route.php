<?php

/*
 * Copyright (c) 2011-2015, Celestino Diaz <celestino.diaz@gmx.de>
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

namespace Brickoo\Component\Routing\Route;

/**
 * Route
 *
 * Defines a route for handling routable requests.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

interface Route {

    /**
     * Returns the unique name of the route.
     * @return string the unique route name
     */
    public function getName();

    /**
     * Returns the route path listening to.
     * @return string the route path listening to
     */
    public function getPath();

    /**
     * Returns the controller responsible for handling this route.
     * @return string the controller class name
     */
    public function getController();

    /**
     * Returns all the regular expression rules available.
     * @return array the regular expression rules if any
     */
    public function getRules();

    /**
     * Checks if the route has rules.
     * @return boolean check result
     */
    public function hasRules();

    /**
     * Sets the route parameter rules.
     * @param array $rules
     * @return \Brickoo\Component\Routing\Route\Route
     */
    public function setRules(array $rules);

    /**
     * Returns the regular expression rule for the passed parameter name.
     * @param string $parameter the parameter name to retrieve the holding rule
     * @throws \UnexpectedValueException if the parameter does not exist
     * @return string the rule assigned to the parameter name
     */
    public function getRule($parameter);

    /**
     * Checks if the parameter has a rule to match.
     * @param string $parameter the parameter name to check
     * @return boolean check result
     */
    public function hasRule($parameter);

    /**
     * Returns all the parameters default values available.
     * @return array the default key-values if any
     */
    public function getDefaultValues();

    /**
     * Returns the default value of the passed parameter name.
     * @param string $parameter the parameter name
     * @throws \UnexpectedValueException if the parameter has not a default value
     * @return mixed the default value for the passed parameter name
     */
    public function getDefaultValue($parameter);

    /**
     * Checks if the rule has a default value.
     * @param string $parameter the parameter name to check
     * @return boolean check result
     */
    public function hasDefaultValue($parameter);

    /**
     * Sets the rules default values.
     * Setting default values makes the rule optional.
     * @param array $defaultValues
     * @return \Brickoo\Component\Routing\Route\Route
     */
    public function setDefaultValues(array $defaultValues);

}
