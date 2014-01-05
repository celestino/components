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

use Brickoo\Log\Logger,
    Brickoo\Network\Client,
    Brickoo\Validation\Argument;

/**
 * Syslog
 *
 * Logs to a common syslog server over an udp protocol socket connection
 * @link http://tools.ietf.org/html/rfc3164
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */

class SyslogLogger implements Logger {

    /**
     * Declaration of the facility constants.
     * @var integer
     */
    const FACILITY_KERNEL            = 0;
    const FACILITY_USER_LEVEL        = 1;
    const FACILITY_MAIL              = 2;
    const FACILITY_DAEMON            = 3;
    const FACILITY_SECURITY          = 4;
    const FACILITY_SYSLOGD           = 5;
    const FACILITY_PRINTER           = 6;
    const FACILITY_NETWORK           = 7;
    const FACILITY_UUCP              = 8;
    const FACILITY_CLOCK             = 9;
    const FACILITY_AUTH              = 10;
    const FACILITY_FTP               = 11;
    const FACILITY_NTP               = 12;
    const FACILITY_LOG               = 13;
    const FACILITY_LOG_ALERT         = 14;
    const FACILITY_CLOCK_DAEMON      = 15;
    const FACILITY_USER_0            = 16;
    const FACILITY_USER_1            = 17;
    const FACILITY_USER_2            = 18;
    const FACILITY_USER_3            = 19;
    const FACILITY_USER_4            = 20;
    const FACILITY_USER_5            = 21;
    const FACILITY_USER_6            = 22;
    const FACILITY_USER_7            = 23;

    /** @var \Brickoo\Network\Client */
    private $networkClient;

    /** @var string */
    private $hostname;

    /** @var string */
    private $serverAddress;

    /** @var integer */
    private $serverPort;

    /** @var integer */
    private $timeout;

    /** @var integer */
    private $facility;

    /**
     * Class constructor.
     * @param \Brickoo\Network\Client $networkClient
     * @param string $hostname the hostname of the maschine running
     * @param string $serverAddress the syslog server address
     * @param integer $serverPort the serevr port, commonly 514
     * @param integer $timeout the connection timeout, default 10 seconds
     * @param integer $facility the facility of the sending messages, default USER_0
     * @throws \InvalidArgumentException if an argument is not valid
     * @return void
     */
    public function __construct(Client $networkClient, $hostname, $serverAddress, $serverPort = 514, $timeout = 10, $facility = self::FACILITY_USER_0) {
        Argument::IsString($hostname);
        Argument::IsString($serverAddress);
        Argument::IsInteger($serverPort);
        Argument::IsInteger($timeout);
        Argument::IsInteger($facility);

        $this->networkClient = $networkClient;
        $this->hostname = $hostname;
        $this->serverAddress = $serverAddress;
        $this->serverPort = $serverPort;
        $this->timeout = $timeout;
        $this->facility = $facility;
    }

    /** {@inheritDoc} */
    public function log($messages, $severity) {
        Argument::IsInteger($severity);

        if (! is_array($messages)) {
            $messages = array($messages);
        }

        $this->sendMessages($messages, $severity);
    }

    /**
     * Sends the  messages to the syslog server.
     * @param array $messages the messages to send
     * @param integer $severity the severity of the message(s)
     * @return void
     */
    private function sendMessages(array $messages, $severity) {

        $this->networkClient->open("udp://". $this->serverAddress, $this->serverPort, $this->timeout, STREAM_CLIENT_CONNECT);

        $messageHeader = $this->getMessageHeader($severity);

        foreach ($messages as $message) {
            $this->networkClient->write($messageHeader ." ". $message);
        }

        $this->networkClient->close();
    }

    /**
     * Returns the log message header.
     * @param integer $severity the severity of the log message(s)
     * @return string the log message header
     */
    private function getMessageHeader($severity) {
        return sprintf("<%d>%s %s", (($this->facility * 8) + $severity), date("c"), $this->hostname);
    }

}