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
 * Exception for an invalid source XML
 */
class XMLException extends IPPException
{
    public function __construct(string $message = "Invalid source XML format", ?Throwable $previous = null)
    {
        parent::__construct($message, ReturnCode::INVALID_XML_ERROR, $previous, false);
    }
}
