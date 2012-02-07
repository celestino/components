<?php

    /*
     * Copyright (c) 2011-2012, Celestino Diaz <celestino.diaz@gmx.de>.
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
     * 3. Neither the name of Brickoo nor the names of its contributors may be used
     *    to endorse or promote products derived from this software without specific
     *    prior written permission.
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

    use Brickoo\Library\Cache\LocalCache;

    // require PHPUnit Autoloader
    require_once ('PHPUnit/Autoload.php');

    /**
     * LocalCacheTest
     *
     * Test suite for the LocalCacheTest class.
     * @see Brickoo\Library\Cache\LocalCacheTest
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class LocalCacheTest extends \PHPUnit_Framework_TestCase
    {

        /**
         * Holds the LocalCacheFixture instance used.
         * @var Brickoo\Library\Cache\LocalCache
         */
        protected $LocalCache;

        /**
         * Set up the LocalCacheFixture object used.
         * @return void
         */
        protected function setUp()
        {
            $this->LocalCache = new LocalCacheFixture();
        }

        /**
         * Test if the LocaclCache does implement the Cache\Interfaces\LocalCacheInterface.
         * @covers Brickoo\Library\Cache\LocalCache::__construct
         */
        public function testConstruct()
        {
            $LocalCache = new LocalCache();
            $this->assertInstanceOf('Brickoo\Library\Cache\Interfaces\LocalCacheInterface', $LocalCache);
        }

        /**
         * Test if the content can be cached under the identifier and the LocalCache reference is returned.
         * @covers Brickoo\Library\Cache\LocalCache::set
         */
        public function testSet()
        {
            $this->assertSame($this->LocalCache, $this->LocalCache->set('unique_identifier', 'some content'));
            $this->assertAttributeEquals
            (
                array('unique_identifier' => 'some content'),
                'cacheValues',
                $this->LocalCache
            );
        }

        /**
         * Test if trying to set an identifier with a wrong argument type throws an exception.
         * @covers Brickoo\Library\Cache\LocalCache::set
         * @expectedException InvalidArgumentException
         */
        public function testSetArgumentException()
        {
            $this->LocalCache->set(array('wrongTpe'), 'some content');
        }

        /**
         * Test if a cached content can be retrieved by its identifier.
         * @covers Brickoo\Library\Cache\LocalCache::get
         * @depends testSet
         */
        public function testGet()
        {
            $this->assertEquals('some content', $this->LocalCache->get('unique_identifier'));
        }

        /**
         * Test if trying to retrieve the cached content with a wrong argument type throws an exception.
         * @covers Brickoo\Library\Cache\LocalCache::get
         * @expectedException InvalidArgumentException
         */
        public function testGetArgumentException()
        {
            $this->LocalCache->get(array('wrongTpe'));
        }

        /**
         * Test if trying to retrieve the content with a not available identifier throws an exception.
         * @covers Brickoo\Library\Cache\LocalCache::get
         * @covers Brickoo\Library\Cache\Exceptions\IdentifierNotAvailableException::__construct
         * @expectedException Brickoo\Library\Cache\Exceptions\IdentifierNotAvailableException
         */
        public function testGetIdentifierException()
        {
            $this->LocalCache->get('fail');
        }

        /**
         * Test if an identifier can be removed from the cached values and the LocalCache reference is returned.
         * @covers Brickoo\Library\Cache\LocalCache::remove
         * @depends testSet
         */
        public function testRemove()
        {
            $this->assertSame($this->LocalCache, $this->LocalCache->remove('unique_identifier'));
            $this->assertAttributeEquals(array(), 'cacheValues', $this->LocalCache);
        }

        /**
         * Test if trying to remove the cached content with a wrong argument type throws an exception.
         * @covers Brickoo\Library\Cache\LocalCache::remove
         * @expectedException InvalidArgumentException
         */
        public function testRemoveArgumentException()
        {
            $this->LocalCache->remove(array('wrongTpe'));
        }

        /**
         * Test if trying to remove the content with a not available identifier throws an exception.
         * @covers Brickoo\Library\Cache\LocalCache::remove
         * @covers Brickoo\Library\Cache\Exceptions\IdentifierNotAvailableException::__construct
         * @expectedException Brickoo\Library\Cache\Exceptions\IdentifierNotAvailableException
         */
        public function testRemoveIdentifierException()
        {
            $this->LocalCache->remove('fail');
        }

        /**
         * Test if the identifier is (not) recognized.
         * @covers Brickoo\Library\Cache\LocalCache::has
         * @depends testSet
         */
        public function testHas()
        {
            $this->assertFalse($this->LocalCache->has('not_available'));
            $this->assertTrue($this->LocalCache->has('unique_identifier'));
        }

        /**
         * Test if the cached values are flushed and the LocalCache reference is returned.
         * @covers Brickoo\Library\Cache\LocalCache::flush
         * @depends testSet
         */
        public function testFlush()
        {
            $this->assertSame($this->LocalCache, $this->LocalCache->flush());
            $this->assertAttributeEquals(array(), 'cacheValues', $this->LocalCache);
        }

    }

    /**
     * Fixture for the LocalCache which contains on cached value.
     * Overrides the constructor to prevent the flushing of the cache values.
     */
    class LocalCacheFixture extends LocalCache
    {
        /**
         * Adding a default value to the cache values.
         * @var array
         */
        protected $cacheValues = array('unique_identifier' => 'some content');

        /**
         * Overriding of the parent constructor.
         * @return void
         */
        public function __construct() {}
    }