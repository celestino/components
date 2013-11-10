<?php

/*
 * Copyright (c) 2011-2013, Celestino Diaz <celestino.diaz@gmx.de>.
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

namespace Brickoo\Log;

use Brickoo\Filesystem\FileObject,
    Brickoo\Log\Logger,
    Brickoo\Validator\Argument;

/**
 * Filesystem
 *
 * Logs the messages to the filesystem.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class FilesystemLogger implements Logger {

    /** @var array */
    private $severityDescription;

    /** @var \Brickoo\Filesystem\fileObject */
    private $fileObject;

    /** @var string */
    private $logsDirectory;

    /**
    * Class constructor.
    * @param \Brickoo\Filesystem\FileObject $fileObject
    * @param string $logsDirectory the directory to store the log messages
    * @return void
    */
    public function __construct(FileObject $fileObject, $logsDirectory) {
        Argument::IsString($logsDirectory);

        $this->fileObject = $fileObject;
        $this->logsDirectory = rtrim($logsDirectory, "\\/") . DIRECTORY_SEPARATOR;
        $this->severityDescription = array(
            Log\Logger::SEVERITY_EMERGENCY    => "Emergency",
            Log\Logger::SEVERITY_ALERT        => "Alert",
            Log\Logger::SEVERITY_CRITICAL     => "Critical",
            Log\Logger::SEVERITY_ERROR        => "Error",
            Log\Logger::SEVERITY_WARNING      => "Warning",
            Log\Logger::SEVERITY_NOTICE       => "Notice",
            Log\Logger::SEVERITY_INFO         => "Info",
            Log\Logger::SEVERITY_DEBUG        => "Debug"
        );
    }

    /** {@inheritDoc} */
    public function log($messages, $severity) {
        Argument::IsInteger($severity);

        if (! is_array($messages)) {
            $messages = array($messages);
        }

        $logMessage = $this->convertToLogMessage($messages, $severity);
        $location = $this->logsDirectory . date("Y-m-d") . ".log";

        $this->fileObject->open($location, "a")->write($logMessage);
        $this->fileObject->close();
    }

    /**
     * Converts the messages passed to one message containing the explained log severity.
     * @param array $messages the messages to convert
     * @param integer $severity the severity to explain for each message
     * @return string the packed log message
     */
    private function convertToLogMessage(array $messages, $severity) {
        Argument::IsInteger($severity);

        if (! array_key_exists($severity, $this->severityDescription)) {
            $severity = Logger::SEVERITY_DEBUG;
        }

        $messagePrefix = sprintf("[%s][%s] ", date("Y-m-d H:i:s"), $this->severityDescription[$severity]);
        return $messagePrefix . implode(PHP_EOL . $messagePrefix, $messages) . PHP_EOL;
    }

}