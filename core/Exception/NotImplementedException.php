<?php
/**
 * IPP - PHP Project Core
 * @author Radim Kocman
 * @author Zbyněk Křivka
 * 
 * DO NOT MODIFY THIS FILE!
 */

namespace IPP\Core\Exception;

/**
 * This exception indicates that a given functionality is not implemented
 */
class NotImplementedException extends InternalErrorException
{
    public function __construct(string $message = "Not implemented")
    {
        parent::__construct($message);
    }
}
