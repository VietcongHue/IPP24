<?php 

// ~~~~~~~~~~~~~~~~~~~~~~~~~
// AUTHOR: Viktor Hančovský
//  login: xhanco00
// ~~~~~~~~~~~~~~~~~~~~~~~~~

namespace IPP\Student;

use IPP\Core\AbstractInterpreter;
use IPP\Core\ReturnCode;
use IPP\Student\Instruction;
use IPP\Student\Exception\InterpretRuntimeException;
use IPP\Student\Frame;
use IPP\Student\Symbol;

class Interpreter extends AbstractInterpreter
{
    /** @var Instruction[] */
    public array $instructions = [];
    /** @var Argument[] */
    public array $arguments = [];
    /** @var Frame[] */
    public array $labels = [];
    public Frame $gf;
    public Frame $tf;
    /** @var Frame[] */
    public array $lf = [];

    public function execute(): int {
        // Check \IPP\Core\AbstractInterpreter for predefined I/O objects:
        $dom = $this->source->getDOMDocument();
        
        // Check if the root element is <program>
        if ($dom->documentElement->nodeName !== 'program') {
            throw new InterpretRuntimeException("Root node is not <program>", ReturnCode::INVALID_SOURCE_STRUCTURE);
        } 
        else {
            if ($dom->documentElement->hasAttribute('language') &&
                strtoupper($dom->documentElement->getAttribute('language')) != 'IPPCODE24') {
                throw new InterpretRuntimeException("Invalid language value", ReturnCode::INVALID_SOURCE_STRUCTURE);
            }
        }

        $name = $description = "";

        if ($dom->documentElement->hasAttribute('name')) {
            $name = $dom->documentElement->getAttribute('name');
        }
        if ($dom->documentElement->hasAttribute('description')) {
            $description = $dom->documentElement->getAttribute('description');
        }

        // Check if there are only <instruction> elements inside <program> element
        foreach ($dom->documentElement->childNodes as $child) {
            if ($child->nodeType === XML_ELEMENT_NODE && $child->nodeName !== 'instruction') {
                // If a non-instruction element is found, return an error
                throw new InterpretRuntimeException("Unknown element",ReturnCode::INVALID_SOURCE_STRUCTURE);
            }

            // Check attributes of each instruction
            if ($child->nodeType === XML_ELEMENT_NODE && $child->nodeName === 'instruction') {
                $ca = $child->attributes;
                $ca_keys = [];
                foreach ($ca as $attr) {
                    $ca_keys[] = $attr->nodeName;
                }

                if (!in_array('order', $ca_keys) || !in_array('opcode', $ca_keys)) {
                    // If order or opcode attribute is missing, return an error
                    throw new InterpretRuntimeException("instruction opcode/order missing", ReturnCode::INVALID_SOURCE_STRUCTURE);
                }

                // Check if there are only <arg1>, <arg2>, <arg3> elements inside each <instruction> element
                foreach ($child->childNodes as $subelem) {
                    if ($subelem->nodeType === XML_ELEMENT_NODE && !preg_match("/^arg[123]$/", $subelem->nodeName)) {
                        // If a non-argument element is found, return an error
                        throw new InterpretRuntimeException("Invalid argument identifier", ReturnCode::INVALID_SOURCE_STRUCTURE);
                    }
                }
            }
        }

        // Proceed with processing the instructions
        foreach ($dom->getElementsByTagName('instruction') as $element) {
            $inst_opcode = $element->getAttribute('opcode');
            $inst_order = $element->getAttribute('order');

            // Check if opcode and order are present
            if ($inst_opcode && $inst_order) {
                $args = $element;
                $this->instructions[] = new Instruction($inst_order, $inst_opcode, $args);
            } 
            else {
                // Handle the error when opcode or order is missing
                throw new InterpretRuntimeException("instruction opcode/order missing", ReturnCode::INVALID_SOURCE_STRUCTURE);
            }
        }

        // Sort instructions based on order
        usort($this->instructions, function ($a, $b) {
            $result = $a->order - $b->order;
            if ($result === 0 || $a->order < 0 || $b->order < 0) {
                throw new InterpretRuntimeException("same/negative instruction order", ReturnCode::INVALID_SOURCE_STRUCTURE);
            }
            return $result;
        });

        $i = 0;
        foreach($this->instructions as $instruction) {
            if ($instruction->opcode == "LABEL") {
                $l_name = $instruction->arguments[0]->value;
                $this->labels[$l_name] = $i;
            }
            $i++;
        }

        $gf = new Frame();

        // Iterate through sorted instructions and create Instruction objects
        foreach ($this->instructions as $instruction) {
            echo $instruction->order . ". " . $instruction->opcode . "\n";
            foreach ($instruction->arguments as $argument) {
                echo "\t" . $argument->order . ". " . $argument->type . ": " . $argument->value . "\n";
            }
        }
        return ReturnCode::OK;
    }

    public function inst_switch($instruction): mixed {
        switch ($instruction->get_opcode()) {
            case 'CREATEFRAME':                                     // <>
                // TODO
                break;
            case 'PUSHFRAME':
                // TODO
                break;
            case 'POPFRAME':
                // TODO
                break;
            case 'RETURN':
                // TODO
                break;
            case 'BREAK':
                // TODO
                break;

            case 'DEFVAR':                                          // <var>
                $varName = $instruction->arguments[0]->value;
                $this->gf->init_variable($varName, null, null);
                break;
            case 'POPS':
                // TODO
                break;

            case 'PUSHS':                                           // <symb>
                // TODO
                break;
            case 'WRITE':        
                $symbol = $this->get_symbol($instruction->arguments[0]);

                switch($symbol->type) {
                    case "int":
                        $this->stdout->writeInt($symbol->value);
                        break;
                    case "bool":
                        $this->stdout->writeBool($symbol->value);
                        break;
                    case "string":
                        $this->stdout->writeString($symbol->value);
                        break;
                    case "nil":
                        $this->stdout->writeString("");
                        break;
                    case "float":
                        $this->stdout->writeFloat($symbol->value);
                        break;
                    default:
                        throw new InterpretRuntimeException("Variable not initialized", ReturnCode::VARIABLE_ACCESS_ERROR);
                }

                // $symb_type = Instruction::get_type($arguments[0], $this->gf, $this->lf);
                // $symb_value = Instruction::get_value($arguments[0], $this->gf, $this->lf);
                
                // // Define the mapping for escape sequences to their corresponding characters
                
            
                // // Replace escape sequences with their corresponding characters
                // foreach ($escape_sequences as $escape_sequence => $replacement) {
                //     $symb_value = str_replace($escape_sequence, $replacement, $symb_value);
                // }

                // if ($symb_type == 'nil') {
                //     $this->stdout->writeString("");
                // }
                // else {
                //     // Write the modified string to stdout
                //     $this->stdout->writeString($symb_value);
                // }
                break;           
            case 'EXIT':
                
                // Get the value of the <symb> argument
                $symb_type = Instruction::get_type($arguments[0], $this->gf, $this->lf);
                $symb_value = Instruction::get_value($arguments[0], $this->gf, $this->lf);
                
                // Check if the value is a valid integer in the range [0, 9]
                if ($symb_type == "int") {
                    // Terminate the interpreter with the corresponding return code
                    return intval($symb_value);
                } 
                else {
                    throw new InterpretRuntimeException("Invalid operand types", 53);
                }
                break;
            case 'DPRINT':
                // TODO
                break;

            case 'CALL':                                            // <label>
                // TODO
                break;
            case 'LABEL':
                // TODO
                break;
            case 'JUMP':
                // TODO
                break;

            case 'MOVE':                                            // <var> <symb>
            
                // Get the value and type of the second argument
                $symb_type = Instruction::get_type($arguments[1], $this->gf, $this->lf);
                $symb_value = Instruction::get_value($arguments[1], $this->gf, $this->lf);
            
                // Print the value and type obtained

                $first_var = $arguments[0]['value'];
                $this->gf[$first_var]['type'] = $symb_type;
                $this->gf[$first_var]['value'] = $symb_value;
                break;
            case 'INT2CHAR':
                $first_var = $arguments[0]['value'];
                
                $symb_type = Instruction::get_type($arguments[1], $this->gf, $this->lf);
                $symb_value = Instruction::get_value($arguments[1], $this->gf, $this->lf);
            
                if ($symb_type !== 'int') {
                    throw new InterpretRuntimeException("Invalid operand types", 53);
                }
            
                if ($symb_value < 0 || $symb_value > 1114111) {
                    throw new InterpretRuntimeException("Invalid integer value", 58);
                }
            
                $char = mb_chr($symb_value, "UTF-8");
                if (!$char) {
                    throw new InterpretRuntimeException("Failed to convert integer to character", 58);
                }
            
                $this->gf[$first_var]['value'] = $char;
                break;
            case 'STRLEN':
                $first_var = $arguments[0]['value'];
            
                $symb_type = Instruction::get_type($arguments[1], $this->gf, $this->lf);
                $symb_value = Instruction::get_value($arguments[1], $this->gf, $this->lf);
            
                if ($symb_type == 'string') {
                    $result = strlen($symb_value);
                    $this->gf[$first_var]['type'] = "int";
                    $this->gf[$first_var]['value'] = $result;
                    return ReturnCode::OK;
                } 
                else {
                    throw new InterpretRuntimeException("Invalid operand types", ReturnCode::OPERAND_TYPE_ERROR);
                }
            case 'TYPE':
                $first_var = $arguments[0]['value'];
                    
                $symb_type = Instruction::get_type($arguments[1], $this->gf, $this->lf);
            
                if ($symb_type == "string") {
                    $this->gf[$first_var]['value'] = "string";
                }
                elseif ($symb_type == "int") {
                    $this->gf[$first_var]['value'] = "int";
                }
                elseif ($symb_type == "bool") {
                    $this->gf[$first_var]['value'] = "bool";
                }
                elseif ($symb_type == "nil") {
                    $this->gf[$first_var]['value'] = "nil";
                }
            
                break;
            case 'NOT':
                $first_var = $arguments[0]['value'];

                $symb1_type = Instruction::get_type($arguments[1], $this->gf, $this->lf);
                $symb1_value = Instruction::get_value($arguments[1], $this->gf, $this->lf);


                if ($symb1_type == "bool") {
                    if ($symb1_value == "true") {
                        $this->gf[$first_var]['type'] = "bool";
                        $this->gf[$first_var]['value'] = "false";
                        
                    }
                    elseif ($symb1_value == "false") {
                        $this->gf[$first_var]['type'] = "bool";
                        $this->gf[$first_var]['value'] = "true";
                    }
                }
                else {
                    throw new InterpretRuntimeException("Invalid operand types", 53);
                }
                break;

            case 'READ':
                $first_var = $arguments[0]['value'];
                                
                $symb1_type = Instruction::get_type($arguments[1], $this->gf, $this->lf);
                $symb1_value = Instruction::get_value($arguments[1], $this->gf, $this->lf);
                                
                if ($symb1_type === 'type' && in_array($symb1_value, ['int', 'string', 'bool'])) {
                    $symb2_type = Instruction::get_type($arguments[2], $this->gf, $this->lf);
                    $symb2_value = Instruction::get_value($arguments[2], $this->gf, $this->lf);
                                
                    if (($symb1_value === 'int' && is_numeric($symb2_value)) ||
                        ($symb1_value === 'string' && is_string($symb2_value)) ||
                        ($symb1_value === 'bool' && is_bool($symb2_value))) {
                        $this->gf[$first_var]['type'] = $symb1_value;
                        $this->gf[$first_var]['value'] = $symb2_value;
                        return ReturnCode::OK;
                    } else {
                        $this->gf[$first_var]['value'] = "nil@nil";
                        throw new InterpretRuntimeException("Invalid operand types", 32);
                    }
                } else {
                    $this->gf[$first_var]['value'] = "nil@nil";
                    throw new InterpretRuntimeException("Invalid operand types", 32);
                }

            case 'ADD':                                             // <var> <symb> <symb>
                $first_var = $arguments[0]['value'];

                $symb1_type = Instruction::get_type($arguments[1], $this->gf, $this->lf);
                $symb1_value = Instruction::get_value($arguments[1], $this->gf, $this->lf);

                $symb2_type = Instruction::get_type($arguments[2], $this->gf, $this->lf);
                $symb2_value = Instruction::get_value($arguments[2], $this->gf, $this->lf);


                if ($symb1_type == 'int' && $symb2_type == 'int') {
                    $result = $symb1_value + $symb2_value;
                    $this->gf[$first_var]['type'] = $symb1_type;
                    $this->gf[$first_var]['value'] = $result;
                }
                else {
                    throw new InterpretRuntimeException("Invalid operand types", 53);
                }
                break;
            case 'SUB':
                $first_var = $arguments[0]['value'];

                $symb1_type = Instruction::get_type($arguments[1], $this->gf, $this->lf);
                $symb1_value = Instruction::get_value($arguments[1], $this->gf, $this->lf);

                $symb2_type = Instruction::get_type($arguments[2], $this->gf, $this->lf);
                $symb2_value = Instruction::get_value($arguments[2], $this->gf, $this->lf);


                if ($symb1_type == 'int' && $symb2_type == 'int') {
                    $result = $symb1_value - $symb2_value;
                    $this->gf[$first_var]['type'] = $symb1_type;
                    $this->gf[$first_var]['value'] = $result;
                }
                else {
                    throw new InterpretRuntimeException("Invalid operand types", 53);
                }
                break;
            case 'MUL':
                $first_var = $arguments[0]['value'];

                $symb1_type = Instruction::get_type($arguments[1], $this->gf, $this->lf);
                $symb1_value = Instruction::get_value($arguments[1], $this->gf, $this->lf);

                $symb2_type = Instruction::get_type($arguments[2], $this->gf, $this->lf);
                $symb2_value = Instruction::get_value($arguments[2], $this->gf, $this->lf);

                if ($symb1_type == 'int' && $symb2_type == 'int') {
                    $result = $symb1_value * $symb2_value;
                    $this->gf[$first_var]['type'] = $symb1_type;
                    $this->gf[$first_var]['value'] = $result;
                }
                else {
                    throw new InterpretRuntimeException("Invalid operand types", 53);
                }
                break;
            case 'IDIV':
                $first_var = $arguments[0]['value'];

                $symb1_type = Instruction::get_type($arguments[1], $this->gf, $this->lf);
                $symb1_value = Instruction::get_value($arguments[1], $this->gf, $this->lf);

                $symb2_type = Instruction::get_type($arguments[2], $this->gf, $this->lf);
                $symb2_value = Instruction::get_value($arguments[2], $this->gf, $this->lf);


                if (($symb1_type == 'int' && $symb2_type == 'int') && ($symb2_value != 0)) {
                    $result = $symb1_value / $symb2_value;
                    $this->gf[$first_var]['type'] = $symb1_type;
                    $this->gf[$first_var]['value'] = (int)$result;
                }
                elseif ($symb2_value == "0") {
                    throw new InterpretRuntimeException("Invalid operand types", 57);
                }
                else {
                    throw new InterpretRuntimeException("Invalid operand types", 53);
                }
                break;
            case 'LT':
                $first_var = $arguments[0]['value'];
            
                $symb1_type = Instruction::get_type($arguments[1], $this->gf, $this->lf);
                $symb1_value = Instruction::get_value($arguments[1], $this->gf, $this->lf);
            
                $symb2_type = Instruction::get_type($arguments[2], $this->gf, $this->lf);
                $symb2_value = Instruction::get_value($arguments[2], $this->gf, $this->lf);
            
                if ($symb1_type == $symb2_type && ($symb1_type != "nil" && $symb2_type != "nil")) {
                    if ($symb1_value < $symb2_value) {
                        $this->gf[$first_var]['type'] = $symb1_type;
                        $this->gf[$first_var]['value'] = "true";
                        
                        return ReturnCode::OK;
                    }
                    else {
                        $this->gf[$first_var]['type'] = $symb1_type;
                        $this->gf[$first_var]['value'] = "false";
                        
                        return ReturnCode::OK;
                    }
                }
                else {
                    throw new InterpretRuntimeException("Invalid operand types", 53);
                }              
            case 'GT':
                $first_var = $arguments[0]['value'];

                $symb1_type = Instruction::get_type($arguments[1], $this->gf, $this->lf);
                $symb1_value = Instruction::get_value($arguments[1], $this->gf, $this->lf);

                $symb2_type = Instruction::get_type($arguments[2], $this->gf, $this->lf);
                $symb2_value = Instruction::get_value($arguments[2], $this->gf, $this->lf);


                
                if ($symb1_type == $symb2_type && ($symb1_type != "nil" && $symb2_type != "nil")) {
                    if ($symb1_value > $symb2_value) {
                        $this->gf[$first_var]['type'] = $symb1_type;
                        $this->gf[$first_var]['value'] = "true";
                        
                        return ReturnCode::OK;
                    }
                    else {
                        $this->gf[$first_var]['type'] = $symb1_type;
                        $this->gf[$first_var]['value'] = "false";
                        
                        return ReturnCode::OK;
                    }
                }
                else {
                    throw new InterpretRuntimeException("Invalid operand types", 53);
                }
            case 'EQ':
                $first_var = $arguments[0]['value'];
            
                $symb1_type = Instruction::get_type($arguments[1], $this->gf, $this->lf);
                $symb1_value = Instruction::get_value($arguments[1], $this->gf, $this->lf);
            
                $symb2_type = Instruction::get_type($arguments[2], $this->gf, $this->lf);
                $symb2_value = Instruction::get_value($arguments[2], $this->gf, $this->lf);
            
                if (($symb1_type == 'int' || $symb1_type == 'bool' || $symb1_type == 'string') && $symb1_type === $symb2_type) {
                    $result = $symb1_value === $symb2_value;
                    $this->gf[$first_var]['type'] = 'bool';
                    $this->gf[$first_var]['value'] = $result ? 'true' : 'false';
                } 
                elseif ($symb1_type == 'nil') {
                    $result = $symb1_value === $symb2_value;
                    $this->gf[$first_var]['type'] = 'bool';
                    $this->gf[$first_var]['value'] = $result ? 'true' : 'false';
                } 
                else {
                    throw new InterpretRuntimeException("Invalid operand types", 53);
                }
                break;
            case 'AND':
                $first_var = $arguments[0]['value'];

                $symb1_type = Instruction::get_type($arguments[1], $this->gf, $this->lf);
                $symb1_value = Instruction::get_value($arguments[1], $this->gf, $this->lf);

                $symb2_type = Instruction::get_type($arguments[2], $this->gf, $this->lf);
                $symb2_value = Instruction::get_value($arguments[2], $this->gf, $this->lf);

                
                if ($symb1_type == 'bool' && $symb2_type == 'bool') {
                    if ($symb1_value == "true" && $symb2_value == "true") {
                        $this->gf[$first_var]['type'] = $symb1_type;
                        $this->gf[$first_var]['value'] = "true";
                        
                        return ReturnCode::OK;
                    }
                    else {
                        $this->gf[$first_var]['type'] = $symb1_type;
                        $this->gf[$first_var]['value'] = "false";
                        
                        return ReturnCode::OK;
                    }
                }
                else {
                    throw new InterpretRuntimeException("Invalid operand types", 53);
                }
            case 'OR':
                $first_var = $arguments[0]['value'];

                $symb1_type = Instruction::get_type($arguments[1], $this->gf, $this->lf);
                $symb1_value = Instruction::get_value($arguments[1], $this->gf, $this->lf);

                $symb2_type = Instruction::get_type($arguments[2], $this->gf, $this->lf);
                $symb2_value = Instruction::get_value($arguments[2], $this->gf, $this->lf);


                
                if ($symb1_type == 'bool' && $symb2_type == 'bool') {
                    if ($symb1_value == "false" && $symb2_value == "false") {
                        $this->gf[$first_var]['type'] = $symb1_type;
                        $this->gf[$first_var]['value'] = "false";
                        
                        return ReturnCode::OK;
                    }
                    else {
                        $this->gf[$first_var]['type'] = $symb1_type;
                        $this->gf[$first_var]['value'] = "true";
                        
                        return ReturnCode::OK;
                    }
                }
                else {
                    throw new InterpretRuntimeException("Invalid operand types", 53);
                }
            case 'STRI2INT':
                $first_var = $arguments[0]['value'];
            
                $symb1_type = Instruction::get_type($arguments[1], $this->gf, $this->lf);
                $symb1_value = Instruction::get_value($arguments[1], $this->gf, $this->lf);
            
                $symb2_type = Instruction::get_type($arguments[2], $this->gf, $this->lf);
                $symb2_value = Instruction::get_value($arguments[2], $this->gf, $this->lf);
            
                if ($symb1_type === "string" && $symb2_type === "int" && $symb2_value >= 0) {
                    if ($symb2_value < strlen($symb1_value)) {
                        $result = mb_ord($symb1_value[$symb2_value], "UTF-8");
                        $this->gf[$first_var]['type'] = "int";
                        $this->gf[$first_var]['value'] = $result;
                    } 
                    else {
                        throw new InterpretRuntimeException("Invalid operand types", 58);
                    }
                } 
                else {
                    throw new InterpretRuntimeException("Invalid operand types", 53);
                }
                break;
            case 'CONCAT':
                $first_var = $arguments[0]['value'];

                $symb1_type = Instruction::get_type($arguments[1], $this->gf, $this->lf);
                $symb1_value = Instruction::get_value($arguments[1], $this->gf, $this->lf);

                $symb2_type = Instruction::get_type($arguments[2], $this->gf, $this->lf);
                $symb2_value = Instruction::get_value($arguments[2], $this->gf, $this->lf);

                
                if ($symb1_type == "string" && $symb2_type == "string") {
                    $result = $symb1_value . $symb2_value;
                    $this->gf[$first_var]['type'] = $symb1_type;
                    $this->gf[$first_var]['value'] = $result;
                    
                    return ReturnCode::OK;
                }
                else {
                    throw new InterpretRuntimeException("Invalid operand types", 53);
                }
            case 'GETCHAR':
                $first_var = $arguments[0]['value'];

                $symb1_type = Instruction::get_type($arguments[1], $this->gf, $this->lf);
                $symb1_value = Instruction::get_value($arguments[1], $this->gf, $this->lf);

                $symb2_type = Instruction::get_type($arguments[2], $this->gf, $this->lf);
                $symb2_value = Instruction::get_value($arguments[2], $this->gf, $this->lf);

                
                if ($symb1_type == "string" && $symb2_type == "int" && $symb2_value >= 0) {
                    if ($symb2_value <= strlen($symb1_value)) {
                        $char = $symb1_value[$symb2_value];
                        $this->gf[$first_var]['type'] = "string";
                        $this->gf[$first_var]['value'] = $char;
                        
                        return ReturnCode::OK;
                    }
                    else {
                        throw new InterpretRuntimeException("Invalid operand types", 58);
                    }
                }
                else {
                    throw new InterpretRuntimeException("Invalid operand types", 53);
                }
            case 'SETCHAR':
                $var_type = Instruction::get_type($arguments[0], $this->gf, $this->lf);
                $var_value = Instruction::get_value($arguments[0], $this->gf, $this->lf);
                            
                $symb1_type = Instruction::get_type($arguments[1], $this->gf, $this->lf);
                $symb1_value = Instruction::get_value($arguments[1], $this->gf, $this->lf);
                            
                $symb2_type = Instruction::get_type($arguments[2], $this->gf, $this->lf);
                $symb2_value = Instruction::get_value($arguments[2], $this->gf, $this->lf);
                            
                // Check if types are correct and index is within bounds
                if ($var_type === 'string' && $symb1_type === 'int' && $symb2_type === 'string') {
                    if ($symb1_value >= 0 && $symb1_value < strlen($var_value)) {
                        // Replace character at the specified index with the new character
                        $var_value[$symb1_value] = $symb2_value[0]; // Ensure only the first character is taken
                        $this->gf[$arguments[0]['value']]['value'] = $var_value;
                        return ReturnCode::OK;
                    } 
                    else {
                        throw new InterpretRuntimeException("Index out of bounds", 58);
                    }
                } 
                else {
                    throw new InterpretRuntimeException("Invalid operand types", 53);
                }
            
            case 'JUMPIFEQ':                                        // <label> <symb> <symb>
                // TODO
                break;
            case 'JUMPIFNEQ':
                // TODO
                break;
            
            default:
                throw new \Exception("Unknown opcode in switch: " . $instruction->get_opcode());
        }
        return ReturnCode::OK;
    }

    private function get_frame_from_name($var_name) {
        $prefix = substr($var_name, 0, 2);
    
        switch ($prefix) {
            case 'GF':
                return $this->gf;
            case 'LF':
                if (count($this->lf) == 0) {
                    return null;
                }
                return $this->lf[0];
            case 'TF':
                if ($this->tf == null) {
                    return null;
                }
                return $this->tf;
            default:
                // handled while parsing argument
        }
    }

    private function get_symbol($arg): Symbol {
        if ($arg->type == "var") {
            $frame = $this->get_frame_from_name($arg->value);
            if (!$frame) {
                throw new InterpretRuntimeException("Frame not initialized/doesnt exist", ReturnCode::FRAME_ACCESS_ERROR);
            }
            return $frame->get_variable(substr($value, 3));
        }
        return new Symbol($arg->value, $arg->type);
    }
}

?>
