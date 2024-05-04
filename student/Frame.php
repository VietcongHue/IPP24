<?php

namespace IPP\Student;

use IPP\Core\ReturnCode;
use IPP\Student\Exception\InterpretRuntimeException;
use IPP\Student\Symbol;

class Frame {
    public array $data;

    public function __construct() {
        $data = [];
    }

    public function init_variable($name, $value, $type) {
        if (array_key_exists($name, $this->data)) {
            throw new InterpretRuntimeException("Variable already exists", ReturnCode::SEMANTIC_ERROR);
        }
        $this->data[$name] = new Symbol($name, $value, $type);
    }

    public function get_variable($name): Symbol {
        if (!array_key_exists($name, $this->data)) {
            throw new InterpretRuntimeException("Variable does not exists", ReturnCode::VARIABLE_ACCESS_ERROR);
        }
        return $this->data[$name];
    }

    public function set_variable($name, $value, $type) {
        if (!array_key_exists($name, $this->data)) {
            throw new InterpretRuntimeException("Variable does not exists", ReturnCode::VARIABLE_ACCESS_ERROR);
        }
        $this->data[$name]->set($value, $type);
    }
}