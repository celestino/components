<?php

    /**
     * Fixture for the apache headers function.
     * @return array sample header values
     */
    function apache_request_headers() {
        return array('Apache-Header' => 'APACHE_SERVER');
    }