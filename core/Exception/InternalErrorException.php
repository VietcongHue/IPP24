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
 * Exception for various internal errors
 */
class InternalErrorException extends IPPException
{
    public function __construct(string $message = "Internal error", ?Throwable $previous = null)
    {
        parent::__construct($message, ReturnCode::INTERNAL_ERROR, $previous);
    }
}
