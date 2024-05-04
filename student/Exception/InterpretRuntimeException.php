<?php

namespace IPP\Student\Exception;

use IPP\Core\Exception\IPPException;

class InterpretRuntimeException extends IPPException{
        public function __construct(string $message, int $code){
            parent::__construct($message,$code);
        }
}