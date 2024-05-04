<?php
/**
 * IPP - PHP Project Core
 * @author Radim Kocman
 * @author Zbyněk Křivka
 * 
 * DO NOT MODIFY THIS FILE!
 */

namespace IPP\Core\Exception;

use IPP\Core\ReturnCode;
use Throwable;

/**
 * Exception for invalid command-line parameters
 */
class ParameterException extends IPPException
{
    public function __construct(string $message = "Invalid parameters (try --help)", ?Throwable $previous = null)
    {
        parent::__construct($message, ReturnCode::PARAMETER_ERROR, $previous, false);
    }
}
