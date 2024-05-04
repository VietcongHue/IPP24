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
 * Exception for output file errors
 */
class OutputFileException extends IPPException
{
    public function __construct(string $message = "Output file error", ?Throwable $previous = null)
    {
        parent::__construct($message, ReturnCode::OUTPUT_FILE_ERROR, $previous, false);
    }
}
