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

    use Brickoo\Validator\TypeValidator;

    /**
     * SyslogNG
     *
     * This class implements the syslog-ng log interface
     * and can be used to log to a common syslog server.
     * @link http://tools.ietf.org/html/rfc3164
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class SyslogNG implements Interfaces\Handler {

        /**
         * Declaration of facility constants.
         * @var integer
         */
        const FACILITY_KERNEL            = 0; // kernel messages
        const FACILITY_USER_LEVEL        = 1; // user-level messages
        const FACILITY_MAIL              = 2; // mail system
        const FACILITY_DAEMON            = 3; // system daemons
        const FACILITY_SECURITY          = 4; // security/authorization messages
        const FACILITY_SYSLOGD           = 5; // messages generated internally by syslogd
        const FACILITY_PRINTER           = 6; // line printer subsystem
        const FACILITY_NETWORK           = 7; // network news subsystem
        const FACILITY_UUCP              = 8; // UUCP subsystem
        const FACILITY_CLOCK             = 9; // clock daemon
        const FACILITY_AUTH              = 10; // security/authorization messages
        const FACILITY_FTP               = 11; // FTP daemon
        const FACILITY_NTP               = 12; // NTP subsystem
        const FACILITY_LOG               = 13; // log audit
        const FACILITY_LOG_ALERT         = 14; // log alert
        const FACILITY_CLOCK_DAEMON      = 15; // clock daemon
        const FACILITY_USER_0            = 16; // local use 0 (local0) (default value)
        const FACILITY_USER_1            = 17; // local use 1 (local1)
        const FACILITY_USER_2            = 18; // local use 2 (local2)
        const FACILITY_USER_3            = 19; // local use 3 (local3)
        const FACILITY_USER_4            = 20; // local use 4 (local4)
        const FACILITY_USER_5            = 21; // local use 5 (local5)
        const FACILITY_USER_6            = 22; // local use 6 (local6)
        const FACILITY_USER_7            = 23; // local use 7 (local7)

        /**
         * Declaration of the severity constants.
         * @var integer
         */
        const SEVERITY_EMERGENCY        = 0; // Emergency: system is unusable
        const SEVERITY_ALERT            = 1; // Alert: action must be taken immediately
        const SEVERITY_CRITICAL         = 2; // Critical: critical conditions
        const SEVERITY_ERROR            = 3; // Error: error conditions
        const SEVERITY_WARNING          = 4; // Warning: warning conditions
        const SEVERITY_NOTICE           = 5; // Notice: normal but significant condition (default value)
        const SEVERITY_INFO             = 6; // Informational: informational messages
        const SEVERITY_DEBUG            = 7; // Debug: debug-level messages

        /**
         * Holds an instance of the SocketObject class.
         * @var \Brickoo\System\SocketObject
         */
        protected $_SocketObject;

        /**
         * Lazy initialization of the SocketObject.
         * Returns the SocketObject instance holded.
         * @return \Brickoo\System\SocketObject
         */
        public function SocketObject() {
            return $this->_SocketObject;
        }

        /**
         * Holds the hostname or IP adress of the server who sent the message.
         * @var string
         */
        protected $hostname;

        /**
         * Returns the current hostname of the server.
         * @throws UnexpectedValueException if the directory is not set
         * @return string
         */
        public function getHostname() {
            if ($this->hostname === null) {
                throw new \UnexpectedValueException('The hostname is `null`.');
            }

            return $this->hostname;
        }

        /**
         * Sets the hostname or IP adress of the current server.
         * @param string $hostname the hostname to set
         * @return \Brickoo\Log\Handler\SyslogNG
         */
        public function setHostname($hostname) {
            TypeValidator::IsStringAndNotEmpty($hostname);

            $this->hostname = str_replace(' ', '_', $hostname);

            return $this;
        }

        /**
         * Holds the server adress of the syslog-ng to log to.
         * @var string
         */
        protected $serverAdress;

        /**
         * Returns the server adress of the syslog-ng used to log.
         * @throws UnexpectedValueException if the server adress is not set
         * @return string
         */
        public function getServerAdress() {
            if ($this->serverAdress === null) {
                throw new \UnexpectedValueException('The server adress is `null`.');
            }
            return $this->serverAdress;
        }

        /**
         * Sets the server adress of the syslog-ng to log to.
         * @param string $serverAdress the server adress to set
         * @return \Brickoo\Log\Handler\SyslogNG
         */
        public function setServerAdress($serverAdress) {
            TypeValidator::IsStringAndNotEmpty($serverAdress);

            $this->serverAdress = $serverAdress;

            return $this;;
        }


        /**
         * Holds the  port of the syslog-ng server.
         * @var integer
         */
        protected $serverPort;

        /**
         * Returns the port number of the syslog-ng server to connect to
         * @throws UnexpectedValueException if the server port is not set
         * @return integer the port number
         */
        public function getServerPort() {
            if ($this->serverPort === null) {
                throw new \UnexpectedValueException('The server port is `null`.');
            }

            return $this->serverPort;
        }

        /**
         * Sets the port of the syslog-ng server to connect to.
         * @param integer $port the syslog-ng port number
         * @return \Brickoo\Log\Handler\SyslogNG
         */
        public function setServerPort($port) {
            TypeValidator::IsInteger($port);

            $this->serverPort = $port;

            return $this;
        }

        /**
         * Holds the timeout for the connection in seconds.
         * @var integer
         */
        protected $timeout;

        /**
         * Returns the timeout of the connection in seconds.
         * @throws UnexpectedValueException if the timeout is not set
         * @return integer the timeout of the connection
         */
        public function getTimeout() {
            if ($this->timeout === null) {
                throw new \UnexpectedValueException('The timeout is `null`.');
            }

            return $this->timeout;
        }

        /**
         * Sets the timeout of the connection to use in seconds.
         * @param integer $timeout the timeout value
         * @return obejct reference
         */
        public function SetTimeout($timeout) {
            TypeValidator::IsInteger($timeout);

            $this->timeout = $timeout;

            return $this;
        }

        /**
         * Holds the facility to use.
         * @var integer
         */
        protected $facility;

        /**
         * Returns the facility used.
         * @throws UnexpectedValueException if the facility is not set
         * @return integer the facility used
         */
        public function getFacility() {
            if ($this->facility === null) {
                throw new \UnexpectedValueException('The facility is `null`.');
            }

            return $this->facility;
        }

        /**
         * Sets the facility to use..
         * @param integer $facility the facility to use
         * @throws OutOfRangeException if the facility is out of range
         * @return \Brickoo\Log\Handler\SyslogNG
         */
        public function setFacility($facility) {
            TypeValidator::IsInteger($facility);

            if (($facility < 0) || ($facility > 23)) {
                throw new \OutOfRangeException('The facility has to be in the range of 0-23.');
            }

            $this->facility = $facility;

            return $this;
        }

        /**
         * Class constructor.
         * Initializes the class properties.
         * @param Brickoo\System\Interfaces\SocketObject $SocketObject
         * @return void
         */
        public function __construct(\Brickoo\System\Interfaces\SocketObject $SocketObject) {
            $this->_SocketObject   = $SocketObject;
        }

        /**
         * Returns the created message header.
         * @param integer $severity the severity of the log message
         * @return string the log message header
         */
        public function getMessageHeader($severity) {
            TypeValidator::IsInteger($severity);

            return '<' . (($this->getFacility() * 8) + $severity) . '>' . date('c') . ' ' . $this->getHostname();
        }

        /**
         * Sends the  messages to the syslog-ng server with the SockeObject.
         * @param array $messages the messages to send
         * @throws Core\Exceptions\UnableToConnectException if the connection can not be created
         * @return \Brickoo\Log\Handler\SyslogNG
         */
        protected function sendMessages(array $messages, $severity) {
            TypeValidator::IsInteger($severity);

            $SocketObject = $this->SocketObject();
            $SocketObject->setProtocol('udp')
                         ->setServerAdress($this->getServerAdress())
                         ->setServerPort($this->getServerPort())
                         ->setTimeout($this->getTimeout());

            $messageHeader = $this->getMessageHeader($severity);

            foreach ($messages as $message) {
                $SocketObject->fwrite($messageHeader . ' ' . $message);
            }

            $SocketObject->close();

            return $this;
        }

        /**
         * Sends the messages to the syslog-ng server.
         * @param array|string $messages the messages to send
         * @param integer $severity the severity of the messages
         * @return \Brickoo\Log\Handler\SyslogNG
         */
        public function log($messages, $severity) {
            TypeValidator::IsInteger($severity);

            if (! is_array($messages)) {
                $messages = array($messages);
            }

            $this->sendMessages($messages, $severity);

            return $this;
        }

	}