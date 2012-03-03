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

    namespace Brickoo\Log\Handler;

    use Brickoo\Log,
        Brickoo\Validator\TypeValidator;

    /**
     * FileHandler
     *
     * Handles the file operations for logging into the filesystem.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class FileHandler implements Interfaces\LogHandlerInterface
    {

        /**
         * Mapping of the severity level description.
         * @var array
         */
        protected $severityDescription;

        /**
         * Holds an instance of the FileObject class.
         * @var \Brickoo\System\Interfaces\FileObjectInterface
         */
        protected $_FileObject;

        /**
         * Returns the FileObject dependency.
         * @return Brickoo\System\Interfaces\FileObjectInterface
         */
        public function FileObject()
        {
            return $this->_FileObject;
        }

        /**
         * Holds the directory for storing the logs.
         * @var string
         */
        protected $directory;

        /**
         * Returns the current assigned directory.
         * @throws \UnexpectedValueException if the directory is not set
         * @return string the current directory used
         */
        public function getDirectory()
        {
            if ($this->directory == null) {
                throw new \UnexpectedValueException('The directory is `null`.');
            }

            return $this->directory;
        }

        /**
         * Sets the full path of the directory to log into.
         * @param string $directory the directory full path
         * @return \Brickoo\Log\Handler\FileHandler
         */
        public function setDirectory($directory)
        {
            TypeValidator::IsString($directory);

            $this->directory = rtrim($directory, '\/') . DIRECTORY_SEPARATOR;

            return $this;
        }

        /**
         * Holds the file prefix.
         * @var string
         */
        protected $filePrefix;

        /**
         * Returns the current file prefix.
         * @return string the file prefix
         */
        public function getFilePrefix()
        {
            return $this->filePrefix;
        }

        /**
         * Sets the log file prefix to use.
         * @param string $filePrefix the file prefix to use
         * @return \Brickoo\Log\Handler\FileHandler
         */
        public function setFilePrefix($filePrefix)
        {
            TypeValidator::IsString($filePrefix, TypeValidator::FLAG_STRING_CAN_BE_EMPTY);

            $this->filePrefix = $filePrefix;

            return $this;
        }

        /**
        * Class constructor.
        * Initializes the class properties.
        * @return void
        */
        public function __construct(\Brickoo\System\Interfaces\FileObjectInterface $FileObject)
        {
            $this->_FileObject    = $FileObject;
            $this->filePrefix      = 'log_';

            $this->severityDescription = array(
                Log\Logger::SEVERITY_EMERGENCY    =>'Emergency',
                Log\Logger::SEVERITY_ALERT        => 'Alert',
                Log\Logger::SEVERITY_CRITICAL     => 'Critical',
                Log\Logger::SEVERITY_ERROR        => 'Error',
                Log\Logger::SEVERITY_WARNING      => 'Warning',
                Log\Logger::SEVERITY_NOTICE       => 'Notice',
                Log\Logger::SEVERITY_INFO         => 'Info',
                Log\Logger::SEVERITY_DEBUG        => 'Debug'
            );
        }

        /**
         * Converts the messages passed to one message containing the explained log severity.
         * @param array $messages the messages to convert
         * @param integer $severity the severity to explain for each message
         * @return string the converted messages
         */
        public function convertToLogMessage(array $messages, $severity)
        {
            TypeValidator::ArrayContainsStrings($messages);
            TypeValidator::IsInteger($severity);

            if (! array_key_exists($severity, $this->severityDescription)) {
                $severity = Log\Logger::SEVERITY_DEBUG;
            }

            $messagePrefix = '[' . date('Y-m-d H:i:s') .'][' . $this->severityDescription[$severity] . '] ';

            return $messagePrefix . implode(PHP_EOL . $messagePrefix, $messages) . PHP_EOL;
        }

        /**
        * Logs the passed messages to the location.
        * @param array|string $messages the messages to log
        * @param integer $severity the severity level to add
        * @return \Brickoo\Log\Handler\FileHandler
        */
        public function log($messages, $severity)
        {
            TypeValidator::IsInteger($severity);

            if (! is_array($messages)) {
                $messages = array($messages);
            }

            $logMessage  = $this->convertToLogMessage($messages, $severity);
            $location    = $this->getDirectory() . $this->getFilePrefix() . date("Y-m-d");

            $this->FileObject()->setMode('a')
                               ->setLocation($location)
                               ->write($logMessage)
                               ->close();

            return $this;
        }

    }