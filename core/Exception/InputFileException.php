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
 * Exception for input file errors
 */
class InputFileException extends IPPException
{
    public function __construct(string $message = "Input file error", ?Throwable $previous = null)
    {
        parent::__construct($message, ReturnCode::INPUT_FILE_ERROR, $previous, false);
    }
}
