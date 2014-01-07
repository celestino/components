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

namespace Brickoo\Tests\Cache\Adapter;

use Brickoo\Cache\Adapter\DoNothingAdapter,
    PHPUnit_Framework_TestCase;

/**
 * DoNothingAdapterTest
 *
 * Test suite for the DoNothing class.
 * @see Brickoo\Cache\Adapter\DoNothingAdapter
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class DoNothingAdapterTest extends PHPUnit_Framework_TestCase {

    /** @covers Brickoo\Cache\Adapter\DoNothingAdapter::get */
    public function testGetReturnsNull() {
        $doNothingAdapter = new DoNothingAdapter();
        $this->assertNull($doNothingAdapter->get('whatever'));
    }

    /** @covers Brickoo\Cache\Adapter\DoNothingAdapter::set */
    public function testSetCacheContent() {
        $doNothingAdapter = new DoNothingAdapter();
        $this->assertSame($doNothingAdapter, $doNothingAdapter->set('whatever', 'non cached content', 60));
    }

    /** @covers Brickoo\Cache\Adapter\DoNothingAdapter::delete */
    public function testDeleteDoesNothing() {
        $doNothingAdapter = new DoNothingAdapter();
        $this->assertSame($doNothingAdapter, $doNothingAdapter->delete('whatever'));
    }

    /** @covers Brickoo\Cache\Adapter\DoNothingAdapter::flush */
    public function testFlushDoesNothing() {
        $doNothingAdapter = new DoNothingAdapter();
        $this->assertSame($doNothingAdapter, $doNothingAdapter->flush());
    }

    /** @covers Brickoo\Cache\Adapter\DoNothingAdapter::isReady */
    public function testIsReadyReturnsAlwaysTrue() {
        $doNothingAdapter = new DoNothingAdapter();
        $this->assertTrue($doNothingAdapter->isReady());
    }

}