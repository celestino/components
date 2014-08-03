<?php

/*
 * Copyright (c) 2011-2014, Celestino Diaz <celestino.diaz@gmx.de>.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 * 1. Redistributions of source code must retain the above copyright
 *    notice, this list of conditions and the following disclaimer.
 * 2. Redistributions in binary form must reproduce the above copyright
 *    notice, this list of conditions and the following disclaimer in the
 *    documentation and/or other materials provided with the distribution.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */

namespace Brickoo\Tests\Component\Routing\Route;

use Brickoo\Component\Routing\Route\GenericRoute,
    PHPUnit_Framework_TestCase;

/**
 * GenericRouteTest
 *
 * Test suite for the GenericRoute class.
 * @see Brickoo\Component\Routing\Route\GenericRoute
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class GenericRouteTest extends PHPUnit_Framework_TestCase {

    /**
     * @covers Brickoo\Component\Routing\Route\GenericRoute::__construct
     * @covers Brickoo\Component\Routing\Route\GenericRoute::getName
     */
    public function testGetName() {
        $this->assertEquals("test.route", $this->getRouteFixture()->getName());
    }

    /** @covers Brickoo\Component\Routing\Route\GenericRoute::getPath */
    public function testGetPath() {
        $this->assertEquals("/article/{name}/{page}", $this->getRouteFixture()->getPath());
    }

    /** @covers Brickoo\Component\Routing\Route\GenericRoute::getController */
    public function testGetController() {
        $this->assertEquals("NewspaperController", $this->getRouteFixture()->getController());
    }

    /** @covers Brickoo\Component\Routing\Route\GenericRoute::setRules */
    public function testSetRules() {
        $expectedRules = ["name" => "[\\w\\-]+", "page" => "[0-9]+"];
        $route = $this->getRouteFixture();
        $this->assertSame($route, $route->setRules($expectedRules));
        $this->assertEquals($expectedRules, $route->getRules());
    }

    /** @covers Brickoo\Component\Routing\Route\GenericRoute::getRules */
    public function testGetRules() {
        $expectedRules = ["name" => "[\\w\\-]+", "page" => "[0-9]+"];
        $this->assertEquals($expectedRules, $this->getRouteFixture($expectedRules)->getRules());
    }

    /** @covers Brickoo\Component\Routing\Route\GenericRoute::getRule */
    public function testGetRule() {
        $route = $this->getRouteFixture(["name" => "[\\w\\-]+", "page" => "[0-9]+"]);
        $this->assertEquals("[\\w\\-]+", $route->getRule("name"));
        $this->assertEquals("[0-9]+", $route->getRule("page"));
    }

    /**
     * @covers Brickoo\Component\Routing\Route\GenericRoute::getRule
     * @expectedException \InvalidArgumentException
     */
    public function testGetRuleThrowsArgumentException() {
        $this->getRouteFixture()->getRule(["wrongType"]);
    }

    /**
     * @covers Brickoo\Component\Routing\Route\GenericRoute::getRule
     * @expectedException \UnexpectedValueException
     */
    public function testGetRuleThrowsUnexpectedException() {
        $this->getRouteFixture()->getRule("rule.does.not.exist");
    }

    /** @covers Brickoo\Component\Routing\Route\GenericRoute::hasRules */
    public function testHasRules() {
        $this->assertFalse($this->getRouteFixture()->hasRules());
        $this->assertTrue($this->getRouteFixture(["name" => "[\\w\\-]+"])->hasRules());
    }

    /** @covers Brickoo\Component\Routing\Route\GenericRoute::hasRule */
    public function testHasRule() {
        $route = $this->getRouteFixture(["name" => "[\\w\\-]+", "page" => "[0-9]+"]);
        $this->assertFalse($route->hasRule("rule.does.not.exist"));
        $this->assertTrue($route->hasRule("name"));
        $this->assertTrue($route->hasRule("page"));
    }

    /**
     * @covers Brickoo\Component\Routing\Route\GenericRoute::hasRule
     * @expectedException \InvalidArgumentException
     */
    public function testHasRuleThrowsArgumentException() {
        $this->getRouteFixture()->hasRule(["wrongType"]);
    }

    /** @covers Brickoo\Component\Routing\Route\GenericRoute::setDefaultValues */
    public function testSetDefaultValues() {
        $expectedResult = ["page" => 1];
        $route = $this->getRouteFixture(["page" => ".+"]);
        $this->assertSame($route, $route->setDefaultValues($expectedResult));
        $this->assertEquals($expectedResult, $route->getDefaultValues());
    }

    /** @covers Brickoo\Component\Routing\Route\GenericRoute::getDefaultValues */
    public function testGetDefaultValues() {
        $expectedResult = ["page" => 1];
        $this->assertEquals($expectedResult, $this->getRouteFixture(["page" => ".+"], $expectedResult)->getDefaultValues());
    }

    /** @covers Brickoo\Component\Routing\Route\GenericRoute::getDefaultValue */
    public function testGetDefaultValue() {
        $this->assertEquals(1, $this->getRouteFixture(["page" => ".+"], ["page" => 1])->getDefaultValue("page"));
    }

    /**
     * @covers Brickoo\Component\Routing\Route\GenericRoute::getDefaultValue
     * @expectedException \InvalidArgumentException
     */
    public function testGetDefaultValueThrowsArgumentException() {
        $this->getRouteFixture()->getDefaultValue(["wrongType"]);
    }

    /**
     * @covers Brickoo\Component\Routing\Route\GenericRoute::getDefaultValue
     * @expectedException \UnexpectedValueException
     */
    public function testGetDefaultValueThrowsUnexpectedValueException() {
        $this->getRouteFixture()->getDefaultValue("rule.does.not.exist");
    }

    /** @covers Brickoo\Component\Routing\Route\GenericRoute::hasDefaultValue */
    public function testHasDefaultValue() {
        $route = $this->getRouteFixture(["page" => ".+"], ["page" => 1]);
        $this->assertFalse($route->hasDefaultValue("rule.does.not.exist"));
        $this->assertTrue($route->hasDefaultValue("page"));
    }

    /**
     * @covers Brickoo\Component\Routing\Route\GenericRoute::hasDefaultValue
     * @expectedException \InvalidArgumentException
     */
    public function testHasDefaultValueThrowsInvalidArgumentException() {
        $this->getRouteFixture()->hasDefaultValue(["wrongType"]);
    }

    /**
     * Returns a generic route fixture.
     * @param array $rules
     * @param array $defaultValues
     * @return \Brickoo\Component\Routing\Route\GenericRoute
     */
    private function getRouteFixture(array $rules = [], array $defaultValues = []) {
        $route = new GenericRoute(
            "test.route",
            "/article/{name}/{page}",
            "NewspaperController",
            "getArticle"
        );
        $route->setRules($rules);
        $route->setDefaultValues($defaultValues);
        return $route;
    }

}
