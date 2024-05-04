<?php
/**
 * IPP - PHP Project Core
 * @author Radim Kocman
 * @author Zbyněk Křivka
 * 
 * DO NOT MODIFY THIS FILE!
 */

namespace IPP\Core\Exception;

use Throwable;

/**
 * Exception for invalid return values from \IPP\Student\Interpreter
 */
class ExecuteReturnCodeException extends InternalErrorException
{
    public function __construct(string $message = "Invalid return value from the interpreter", ?Throwable $previous = null)
    {
        parent::__construct($message, $previous);
    }
}
