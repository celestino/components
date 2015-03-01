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

use Brickoo\Component\IO\Stream\SocketStream;
use Brickoo\Component\IO\Stream\StreamWriter;
use Brickoo\Component\Common\Assert;

/**
 * SyslogLogger
 *
 * Logs to a common syslog server over a socket stream.
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

    /** @var \Brickoo\Component\IO\Stream\SocketStream */
    private $socketStream;

    /** @var string */
    private $hostname;

    /** @var integer */
    private $facility;

    /**
     * Class constructor.
     * @param \Brickoo\Component\IO\Stream\SocketStream $socketStream
     * @param string $hostname the hostname of the machine running
     * @param integer $facility the facility of the sending messages, default USER_0
     * @throws \InvalidArgumentException if an argument is not valid
     */
    public function __construct(SocketStream $socketStream, $hostname, $facility = self::FACILITY_USER_0) {
        Assert::isString($hostname);
        Assert::isInteger($facility);

        $this->socketStream = $socketStream;
        $this->hostname = $hostname;
        $this->facility = $facility;
    }

    /** {@inheritDoc} */
    public function log($messages, $severity) {
        Assert::isInteger($severity);

        if (! is_array($messages)) {
            $messages = [$messages];
        }

        $this->sendMessages($messages, $severity);
        return $this;
    }

    /**
     * Sends the  messages to the syslog server.
     * @param array $messages the messages to send
     * @param integer $severity the severity of the message(s)
     * @return void
     */
    private function sendMessages(array $messages, $severity) {
        $streamWriter = new StreamWriter($this->socketStream->open());

        $messageHeader = $this->getMessageHeader($severity);
        foreach ($messages as $message) {
            $streamWriter->write($messageHeader." ".$message);
        }

        $this->socketStream->close();
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
