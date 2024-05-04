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
 * Exception for ill-defined student classes
 */
class IntegrationException extends IPPException
{
    public function __construct(string $message = "Integration error", ?Throwable $previous = null)
    {
        parent::__construct($message, ReturnCode::INTEGRATION_ERROR, $previous);
    }
}
