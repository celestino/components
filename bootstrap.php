<?php

    namespace Brickoo;

    /*
     * Copyright (c) 2008-2011, Celestino Diaz Teran <celestino@users.sourceforge.net>.
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

    use Brickoo\Library\Core\Autoloader;

    /**
     * Bootstrap for the Brickoo Framework.
     * Defines framework constants and initializes the required autoloader.
     * @author Celestino Diaz Teran <celestino@users.sourceforge.net>
     */

    // set error reporting to strict (optional)
    error_reporting ( E_ALL | E_STRICT );

    // enable displaying runtime errors while development(optional)
    ini_set ('display_errors', 1);

    // set default timezone for Date functions (optional)
    date_default_timezone_set ('UTC');

    // define the Brickoo Framework root directory
    if (! defined ('BRICKOO_DIR'))
    {
        define ('BRICKOO_DIR',  realpath(dirname(__FILE__)) . DIRECTORY_SEPARATOR);
    }

    // require the default autoloader must implement the AutoloaderInterface
    require_once ('Library'. DIRECTORY_SEPARATOR .'Core'. DIRECTORY_SEPARATOR .'Autoloader.php');

    // create the class autoloader instance and register the Brickoo namespace
    $Autoloader = new Autoloader();

    // register the brickoo path to the autoloader
    $Autoloader->registerNamespace('Brickoo', BRICKOO_DIR);

    // register the assigned autoloader to php
    $Autoloader->registerAutoloader();

?>