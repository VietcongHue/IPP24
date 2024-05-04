<?php
/**
 * IPP - PHP Project Core
 * @author Radim Kocman
 * @author Zbyněk Křivka
 * 
 * DO NOT MODIFY THIS FILE!
 */

namespace IPP\Core;

/**
 * Common script return codes from the project specification
 */
abstract class ReturnCode
{
    const int OK = 0;
    const int PARAMETER_ERROR = 10;
    const int INPUT_FILE_ERROR = 11;
    const int OUTPUT_FILE_ERROR = 12;
    const int INVALID_XML_ERROR = 31;
    const int INVALID_SOURCE_STRUCTURE = 32;
    const int SEMANTIC_ERROR = 52;
    const int OPERAND_TYPE_ERROR = 53;
    const int VARIABLE_ACCESS_ERROR = 54;
    const int FRAME_ACCESS_ERROR = 55;
    const int VALUE_ERROR = 56;
    const int OPERAND_VALUE_ERROR = 57;
    const int STRING_OPERATION_ERROR = 58;
    const int INTEGRATION_ERROR = 88;
    const int INTERNAL_ERROR = 99;
}
