<?php

namespace IPP\Student;

use DOMDocument;
use Exception;
use IPP\Core\ReturnCode;
use IPP\Student\Exception\InterpretRuntimeException;
use IPP\Student\Argument;

class Instruction {
    public string $opcode;
    public int $order;
    public array $arguments = [];

    public static $inst_dict = [
        "CREATEFRAME" => [],
        "PUSHFRAME" => [],
        "POPFRAME" => [],
        "RETURN" => [],
        "BREAK" => [],

        "DEFVAR" => ["var"],
        "POPS" => ["var"],

        "PUSHS" => ["symb"],
        "WRITE" => ["symb"],
        "EXIT" => ["symb"],
        "DPRINT" => ["symb"],

        "CALL" => ["label"],
        "LABEL" => ["label"],
        "JUMP" => ["label"],

        "MOVE" => ["var", "symb"],
        "INT2CHAR" => ["var", "symb"],
        "STRLEN" => ["var", "symb"],
        "TYPE" => ["var", "symb"],
        "NOT" => ["var", "symb"],

        "READ" => ["var", "type"],

        "ADD" => ["var", "symb", "symb"],
        "SUB" => ["var", "symb", "symb"],
        "MUL" => ["var", "symb", "symb"],
        "IDIV" => ["var", "symb", "symb"],
        "LT" => ["var", "symb", "symb"],
        "GT" => ["var", "symb", "symb"],
        "EQ" => ["var", "symb", "symb"],
        "AND" => ["var", "symb", "symb"],
        "OR" => ["var", "symb", "symb"],
        "STRI2INT" => ["var", "symb", "symb"],
        "CONCAT" => ["var", "symb", "symb"],
        "GETCHAR" => ["var", "symb", "symb"],
        "SETCHAR" => ["var", "symb", "symb"],

        "JUMPIFEQ" => ["label", "symb", "symb"],
        "JUMPIFNEQ" => ["label", "symb", "symb"],
    ];

    public function __construct($order, $opcode, $args) {
        $this->order = intval($order);
        if ($this->order === 0 && $order !== "0") {
            throw new InterpretRuntimeException("Instruction order must be an integer", ReturnCode::INVALID_SOURCE_STRUCTURE);
        }

        $this->opcode = $opcode;

        $this->process_arguments($args);
        $this->valid_inst($opcode);
    }

    public function valid_inst($opcode) {
        // Check if $opcode is in dictionary
        if (!array_key_exists($opcode, self::$inst_dict)) {
            // Handle error: Opcode not found
            throw new InterpretRuntimeException("Opcode not found", ReturnCode::INVALID_SOURCE_STRUCTURE);
        }

        $expected_args = self::$inst_dict[$opcode];
        
        if (count($expected_args) != count($this->arguments)) {
            throw new InterpretRuntimeException("Incorrect amount of arguments", ReturnCode::INVALID_SOURCE_STRUCTURE);
        }

        for ($i = 0; $i < count($expected_args); $i++) {
            // symb may be a constant or a variable
            if ($expected_args[$i] == "symb") {
                if (!in_array($this->arguments[$i]->type, ["int", "bool", "string", "nil", "float", "var"])) {
                    throw new InterpretRuntimeException("Incorrect argument type", ReturnCode::INVALID_SOURCE_STRUCTURE); 
                }
            }
            else if ($expected_args[$i] != $this->arguments[$i]->type) {
                throw new InterpretRuntimeException("Incorrect argument type", ReturnCode::INVALID_SOURCE_STRUCTURE);
            }
        }
    }
    
    public function process_arguments($input_args) {
        foreach ($input_args->childNodes as $oneNode) {
            if ($oneNode->nodeType === XML_ELEMENT_NODE) {
                $arg_value = trim($oneNode->nodeValue);
                $arg_type = $oneNode->getAttribute('type');
                $this->arguments[] = new Argument($oneNode->nodeName, $arg_type, $arg_value);
            }
        }

        // Sort the arguments based on their keys (arg1, arg2, arg3)
        usort($this->arguments, function ($a, $b) {
            $result = $a->order - $b->order;
            if ($result === 0 || $a->order < 0 || $b->order < 0) {
                throw new InterpretRuntimeException("same/negative argument order", ReturnCode::INVALID_SOURCE_STRUCTURE);
            }
            return $result;
        });

        // last element order, check
        $lastArgumentOrder = end($this->arguments)->order;
        if (count($this->arguments) !== $lastArgumentOrder) {
            throw new InterpretRuntimeException("Number of arguments does not match the order of the last argument", ReturnCode::INVALID_SOURCE_STRUCTURE);
        }
    }

    public static function isValidVariableName($var_name, &$gf, &$lf, &$variableValue) {
        if (isset($gf[$var_name])) {
            // Set $variableValue to the whole array containing type and value
            $variableValue = $gf[$var_name];
            return true;
        } 
        elseif (isset($lf[$var_name])) {
            // Set $variableValue to the whole array containing type and value
            $variableValue = $lf[$var_name];
            return true;
        } 
        else {
            print("Variable doesn't exist\n");
            return false;
        }
    }

    // Method to get the value of an argument
    public static function get_value($argument, &$gf, &$lf) {
        $value = $argument['value'];
        if (preg_match("/^(GF|LF|TF)@[a-zA-Z_][a-zA-Z0-9_]*$/", $value)) {
            $frame = explode('@', $value)[0];
            if ($frame === 'GF' && isset($gf[$value])) {
                return $gf[$value]['value'];
            } 
            elseif ($frame === 'LF' && isset($lf[$value])) {
                return $lf[$value]['value'];
            } 
            else {
                return "Variable $value not found in frame $frame";
            }
        } 
        else {
            return $value;
        }
    }

    // Method to get the type of an argument
    public static function get_type($argument, &$gf, &$lf) {
        $value = $argument['value'];
        if (preg_match("/^(GF|LF|TF)@[a-zA-Z_][a-zA-Z0-9_]*$/", $value)) {
            $frame = explode('@', $value)[0];
            if ($frame === 'GF' && isset($gf[$value])) {
                return $gf[$value]['type'];
            } 
            elseif ($frame === 'LF' && isset($lf[$value])) {
                return $lf[$value]['type'];
            } 
            else {
                return "Variable $value not found in frame $frame";
            }
        } 
        else {
            return $argument['type'];
        }
    }

    public function print_instruction($arguments) {
        print("Order: " . $this->order . ", Opcode: " . $this->opcode . "\n");
        
        // Print arguments if available
        if (!empty($arguments)) {
            foreach ($arguments as $i => $arg) {
                echo "\targ" . ($i + 1) . ": {$arg['value']}\ttype: {$arg['type']}\n";
            }
            print("\n");
        }
    }
}

?>