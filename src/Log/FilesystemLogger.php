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

namespace Brickoo\Component\Log;

use Brickoo\Component\Validation\Argument;

/**
 * Filesystem
 *
 * Logs the messages to the filesystem.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class FilesystemLogger implements Logger {

    /** @var array */
    private $severityDescription;

    /** @var string */
    private $logsDirectory;

    /**
    * Class constructor.
    * @param string $logsDirectory the directory to store the log messages
    */
    public function __construct($logsDirectory) {
        Argument::isString($logsDirectory);

        $this->logsDirectory = rtrim($logsDirectory, "\\/").DIRECTORY_SEPARATOR;
        $this->severityDescription = [
            Logger::SEVERITY_EMERGENCY    => "Emergency",
            Logger::SEVERITY_ALERT        => "Alert",
            Logger::SEVERITY_CRITICAL     => "Critical",
            Logger::SEVERITY_ERROR        => "Error",
            Logger::SEVERITY_WARNING      => "Warning",
            Logger::SEVERITY_NOTICE       => "Notice",
            Logger::SEVERITY_INFO         => "Info",
            Logger::SEVERITY_DEBUG        => "Debug"
        ];
    }

    /** {@inheritDoc} */
    public function log($messages, $severity) {
        Argument::isInteger($severity);

        if (! is_array($messages)) {
            $messages = [$messages];
        }

        $logMessage = $this->convertToLogMessage($messages, $severity);
        $location = $this->logsDirectory.date("Y-m-d").".log";

        $file = fopen($location, "a");
        fwrite($file, $logMessage);
        fclose($file);

        return $this;
    }

    /**
     * Converts the messages passed to one message containing the explained log severity.
     * @todo throw exception if severity is unknown
     * @param array $messages the messages to convert
     * @param integer $severity the severity to explain for each message
     * @return string the packed log message
     */
    private function convertToLogMessage(array $messages, $severity) {
        Argument::isInteger($severity);

        $messagePrefix = sprintf("[%s][%s] ", date("Y-m-d H:i:s"), $this->severityDescription[$severity]);
        return $messagePrefix.implode(PHP_EOL.$messagePrefix, $messages).PHP_EOL;
    }

}
