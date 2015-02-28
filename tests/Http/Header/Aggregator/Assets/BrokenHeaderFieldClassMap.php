<?php

namespace Brickoo\Tests\Component\Http\Header\Aggregator\Assets;

use Brickoo\Component\Http\Header\Aggregator\HeaderFieldClassMap;

class BrokenHeaderFieldClassMap extends HeaderFieldClassMap {

    /** Add an unavailable header field class */
    public function __construct() {
        $this->map["X-Broken-Field"] = "Brickoo\\Test\\Component\\Http\\Header\\XBrokenField";
    }

}
