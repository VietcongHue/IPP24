<?php

namespace IPP\Student;

use IPP\Core\ReturnCode;
use IPP\Student\Exception\InterpretRuntimeException;

class Symbol {
    public $name;
    public $type;
    public $value;

    public function __construct($value, $type, $name = null)
    {
        $this->name = $name;
        $this->value = $value;
        $this->type = $type;
    }

    public function set($value, $type) {
        $this->value = $value;
        $this->type = $type;
    }
}