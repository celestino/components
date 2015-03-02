<?php

namespace Brickoo\Component\Log\Exception;

use Brickoo\Component\Log\Exception;

/**
 * Exception
 *
 * Exception thrown if severity could not be resolved.
 * @author Celestino Diaz <celestino.diaz@gmx.de>
 */
class UnknownSeverityException extends Exception {

    /**
     * Class constructor.
     * Calls the parent exception constructor.
     * @param integer $severity
     * @param null|\Exception $previousException
     */
    public function __construct($severity, \Exception $previousException = null) {
        parent::__construct(sprintf("Severity key `%d` unknown.", $severity), 0, $previousException);
    }

}
