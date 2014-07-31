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
namespace Brickoo\Component\IO\Stream;

use Brickoo\Component\Validation\Argument;

/**
 * FileStreamConfig
 *
 * Implements a configuration for file based streams.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class FileStreamConfig {

    /** @var  string */
    private $filename;

    /** @var integer */
    private $mode;

    /** @var boolean */
    private $useIncludePath;

    /** @var array */
    private $context;

    /**
     * @param string $filename
     * @param integer $mode
     * @param boolean $useIncludePath
     * @param array $context
     * @throws \InvalidArgumentException
     */
    public function __construct($filename, $mode, $useIncludePath = false, array $context = array()) {
        Argument::IsString($filename);
        Argument::IsInteger($mode);
        Argument::IsBoolean($useIncludePath);

        $this->filename = $filename;
        $this->mode = $mode;
        $this->useIncludePath = $useIncludePath;
        $this->context = $context;
    }

    /**
     * Returns the configuration file name.
     * @return string the file name
     */
    public function getFilename() {
        return $this->filename;
    }

    /**
     * Returns the file accessing mode.
     * @return integer the file accessing mode
     */
    public function getMode() {
        return $this->mode;
    }

    /**
     * Checks if the include path should also be used.
     * @return boolean check result
     */
    public function shouldUseIncludePath() {
        return $this->useIncludePath;
    }

    /**
     * Returns the file stream context options.
     * @return array the context options
     */
    public function getContext() {
        return $this->context;
    }

}
