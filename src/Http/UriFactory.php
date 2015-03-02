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

namespace Brickoo\Component\Http;

use Brickoo\Component\Http\Aggregator\UriAggregator;

/**
 * UriFactory
 *
 * Implements a http uri factory.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class UriFactory {

    /**
     * Creates a uri instance.
     * @param \Brickoo\Component\Http\Aggregator\UriAggregator $uriAggregator
     * @return \Brickoo\Component\Http\Uri
     */
    public function create(UriAggregator $uriAggregator) {
        return new Uri(
            $uriAggregator->getScheme(),
            $this->createAuthority($uriAggregator),
            $uriAggregator->getPath(),
            $this->createQuery($uriAggregator),
            $uriAggregator->getFragment()
        );
    }

    /**
     * Creates the authority dependency.
     * @param \Brickoo\Component\Http\Aggregator\UriAggregator $uriAggregator
     * @return \Brickoo\Component\Http\UriAuthority
     */
    private function createAuthority(UriAggregator $uriAggregator) {
        return new UriAuthority($uriAggregator->getHostname(), $uriAggregator->getPort());
    }

    /**
     * Creates the query dependency.
     * @param \Brickoo\Component\Http\Aggregator\UriAggregator $uriAggregator
     * @return \Brickoo\Component\Http\UriQuery
     */
    private function createQuery(UriAggregator $uriAggregator) {
        return (new UriQuery())->fromString($uriAggregator->getQueryString());
    }

}
