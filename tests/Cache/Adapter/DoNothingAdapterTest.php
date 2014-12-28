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

namespace Brickoo\Tests\Component\Cache\Adapter;

use Brickoo\Component\Cache\Adapter\DoNothingAdapter;
use PHPUnit_Framework_TestCase;

/**
 * DoNothingAdapterTest
 *
 * Test suite for the DoNothing class.
 * @see Brickoo\Component\Cache\Adapter\DoNothingAdapter
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class DoNothingAdapterTest extends PHPUnit_Framework_TestCase {

    /** @covers Brickoo\Component\Cache\Adapter\DoNothingAdapter::get */
    public function testGetReturnsNull() {
        $doNothingAdapter = new DoNothingAdapter();
        $this->assertNull($doNothingAdapter->get('whatever'));
    }

    /** @covers Brickoo\Component\Cache\Adapter\DoNothingAdapter::set */
    public function testSetCacheContent() {
        $doNothingAdapter = new DoNothingAdapter();
        $this->assertSame($doNothingAdapter, $doNothingAdapter->set('whatever', 'non cached content', 60));
    }

    /** @covers Brickoo\Component\Cache\Adapter\DoNothingAdapter::delete */
    public function testDeleteDoesNothing() {
        $doNothingAdapter = new DoNothingAdapter();
        $this->assertSame($doNothingAdapter, $doNothingAdapter->delete('whatever'));
    }

    /** @covers Brickoo\Component\Cache\Adapter\DoNothingAdapter::flush */
    public function testFlushDoesNothing() {
        $doNothingAdapter = new DoNothingAdapter();
        $this->assertSame($doNothingAdapter, $doNothingAdapter->flush());
    }

    /** @covers Brickoo\Component\Cache\Adapter\DoNothingAdapter::isReady */
    public function testIsReadyReturnsAlwaysTrue() {
        $doNothingAdapter = new DoNothingAdapter();
        $this->assertTrue($doNothingAdapter->isReady());
    }

}
