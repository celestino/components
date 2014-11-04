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

namespace Brickoo\Tests\Component\Http\Aggregator\Strategy;

use Brickoo\Component\Http\Aggregator\Strategy\PhpHeaderAggregatorStrategy;
use PHPUnit_Framework_TestCase;

/**
 * PhpHeaderAggregatorStrategy
 *
 * Test suite for the PhpHeaderAggregatorStrategy class.
 * @see Brickoo\Component\Http\Aggregator\PhpHeaderAggregatorStrategy
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class PhpHeaderAggregatorStrategyTest extends PHPUnit_Framework_TestCase {

    /**
     * @cover Brickoo\Component\Htt\Aggregator\Aggregator\PhpHeaderAggregatorStrategy::getHeaders
     * @cover Brickoo\Component\Htt\Aggregator\Aggregator\PhpHeaderAggregatorStrategy::getPhpExtractedHttpHeaders
     */
    public function testGetHeadersFromGlobalServerValues() {
        if (defined("HHVM_VERSION")) {
            $this->markTestSkipped(
                "Unsupported routine by HHVM v3.1.0\n".
                "https://github.com/facebook/hhvm/issues/985"
            );
        }

        if (! function_exists("apache_request_headers")) {
            require_once realpath(__DIR__)."/Assets/requiredFunctions.php";
        }

        $expectedHeaders = ["CONNECTION" => "keep-alive", "X-Unit-Test" => "ok"];
        $_SERVER["HTTP_CONNECTION"] = "keep-alive";
        $requestHeaderAggregatorStrategy = new PhpHeaderAggregatorStrategy();
        $this->assertEquals($expectedHeaders, $requestHeaderAggregatorStrategy->getHeaders());
    }

}
