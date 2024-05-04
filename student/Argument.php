<?php

namespace IPP\Student;

use IPP\Core\ReturnCode;
use IPP\Student\Exception\InterpretRuntimeException;

class Argument {
    public $order;
    public $type;
    public $value;

    public function __construct($arg_tag, $arg_type, $arg_value) {
        $this->order = $this->valid_order($arg_tag);
        $this->type = $arg_type;

        // ["int", "bool", "string", "nil", "float", "var", "type", "label"]
        switch($this->type) {
            case "int":
                $this->value = $this->valid_type_int($arg_value);
                break;
            case "bool":
                $this->value = $this->valid_type_bool($arg_value);
                break;
            case "string":
                $this->value = $this->valid_type_string($arg_value);
                break;
            case "nil":
                $this->value = $this->valid_type_nil($arg_value);
                break;
            case "float":
                $this->value = $this->valid_type_float($arg_value);
                break;
            case "var":
                $this->value = $this->valid_type_var($arg_value);
                break;
            case "type":
                $this->value = $this->valid_type_type($arg_value);
                break;
            case "label":
                $this->value = $this->valid_type_label($arg_value);
                break;
            default:
                // FIXME unknown type - PRAVDEPODOBNE 52?
                throw new InterpretRuntimeException("Unknown argument type", ReturnCode::SEMANTIC_ERROR);

        }
    }

    public function valid_order($tag_name) {
        $order_rgx = "/^arg[123]$/";

        if (!preg_match($order_rgx, $tag_name)) {
            throw new InterpretRuntimeException("argument tag not matching regex", ReturnCode::INVALID_SOURCE_STRUCTURE);
        }

        return (int)substr($tag_name, 3);
    }

    public function valid_type_int($value) {
        if (preg_match('/^int@(-|\+)?(0x[0-9A-Fa-f]{1,16}|\d+|0o[0-7]{1,7})$/', $value)) {
            $intValue = (int)$value;
            if ((string)$intValue === $value) {
                return $intValue;
            }
        }
        throw new InterpretRuntimeException("Invalid int type value", ReturnCode::OPERAND_TYPE_ERROR);
    }    

    public function valid_type_bool($value) {
        if ($value !== "true" || $value !== "false") {
            throw new InterpretRuntimeException("Invalid bool type value", ReturnCode::OPERAND_TYPE_ERROR);
        }
        return true;
    }

    public function valid_type_string($value) {
        $replaceEscapes = function($matches) {
            $map = [ 
                '\009' => "\t", 
                '\010' => "\n", 
                '\032' => " ",
                '\035' => "#", 
                '\092' => "\\"
            ];
            if(isset($map[$matches[0]])) {
                return $map[$matches[0]];
            }
            return $matches[0];
        };

        $value = preg_replace_callback('/\\\\0[0-3][0-7]{2}|\\\\035|\\\\092/', $replaceEscapes, $value);
        
        return $value;
    }    

    public function valid_type_nil($value) {
        if ($value !== "nil") {
            throw new InterpretRuntimeException("Invalid nil type value", ReturnCode::OPERAND_TYPE_ERROR);
        }
        return true;
    }

    public function valid_type_float($value) {
        if (is_numeric($value)) {
            $floatValue = (float)$value;
            if ((string)$floatValue === $value) {
                return $value;
            }
        }
        throw new InterpretRuntimeException("Invalid float type value", ReturnCode::OPERAND_TYPE_ERROR);
    }

    public function valid_type_var($value) {
        $var_rgx = '/^(LF|GF|TF)@[a-zA-Z_\-$&%*!?][a-zA-Z_\-$&%*!?0-9]*$/';
        if (!preg_match($var_rgx, $value)) {
            throw new InterpretRuntimeException("Invalid variable format: $value", ReturnCode::OPERAND_TYPE_ERROR);
        }
        return $value;
    }

    public function valid_type_type($value) {
        if (!in_array($value, ["bool", "int", "string"])) {
            throw new InterpretRuntimeException("Invalid variable format: $value", ReturnCode::OPERAND_TYPE_ERROR);
        }
        return $value;
    }

    public function valid_type_label($value) {
        $label_rgx = '/^[a-zA-Z_\-$&%*!?][\w\-$&%*!?]*$/';
        if (!preg_match($label_rgx, $value)) {
            throw new InterpretRuntimeException("Invalid variable format: $value", ReturnCode::OPERAND_TYPE_ERROR);
        }
        return $value;
    }
}