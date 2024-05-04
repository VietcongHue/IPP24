<?php
/**
 * IPP - PHP Project Core
 * @author Radim Kocman
 * @author Zbyněk Křivka
 * 
 * DO NOT MODIFY THIS FILE!
 */

namespace IPP\Core;

use IPP\Core\Exception\ParameterException;
use IPP\Core\Interface\InputReader;
use IPP\Core\Interface\OutputWriter;
use IPP\Core\Interface\SourceReader;

/**
 * This class handles command-line parameters and prepares appropriate I/O objects.
 * It is possible to extend this class in \IPP\Student\Settings.
 */
class Settings
{
    /** @var string[] */
    protected array $longOptions = [
        "help",
        "source:",
        "input:"
    ];

    protected bool $help = false;
    protected string $source = "";
    protected string $input = "";

    final public function __construct()
    {}

    public function processArgs(): void
    {
        $options = getopt("", $this->longOptions, $restIndex);
        $options = $options ?: [];

        if (array_splice($_SERVER['argv'], $restIndex)) {
            throw new ParameterException;
        }

        if (isset($options['help'])) {
            $this->help = true;
            if (count($options) > 1) {
                throw new ParameterException;
            }
            return;
        }
        
        $source = $options['source'] ?? null;
        $source = is_string($source)? $source : null;
        $input = $options['input'] ?? null;
        $input = is_string($input)? $input : null;
        if (!isset($source) && !isset($input)) {
            throw new ParameterException;
        }
        $this->source = $source ?? "php://stdin";
        $this->input = $input ?? "php://stdin";
    }

    public function getHelpString(): string
    {
        return "IPPcode24 Interpreter\n" 
            ."Usage: php interpret.php [options...]\n"
            ."\n"
            ."Options:\n"
            ."--help            : displays this help and exits\n"
            ."--source=<file>   : source code in XML format\n"
            ."--input=<file>    : user inputs\n"
            ."At least one of the options --source and --input has to be specified.\n"
            ."For the unspecified option, STDIN is used instead of a file.\n"
            ."\n"
            ."Return codes:\n"
            ."0-9 : correct execution\n"
            .ReturnCode::PARAMETER_ERROR
            ."  : invalid parameters\n"
            .ReturnCode::INPUT_FILE_ERROR
            ."  : input file error\n"
            .ReturnCode::OUTPUT_FILE_ERROR
            ."  : output file error\n"
            .ReturnCode::INVALID_XML_ERROR
            ."  : invalid source XML format\n"
            .ReturnCode::INVALID_SOURCE_STRUCTURE
            ."  : invalid source structure\n"
            .ReturnCode::SEMANTIC_ERROR
            ."  : semantic error\n"
            .ReturnCode::OPERAND_TYPE_ERROR
            ."  : runtime error - bad operand types\n"
            .ReturnCode::VARIABLE_ACCESS_ERROR
            ."  : runtime error - non-existent variable\n"
            .ReturnCode::FRAME_ACCESS_ERROR
            ."  : runtime error - non-existent frame\n"
            .ReturnCode::VALUE_ERROR
            ."  : runtime error - missing value\n"
            .ReturnCode::OPERAND_VALUE_ERROR
            ."  : runtime error - bad operand value\n"
            .ReturnCode::STRING_OPERATION_ERROR
            ."  : runtime error - bad string operation\n"
            .ReturnCode::INTEGRATION_ERROR
            ."  : integration error\n"
            .ReturnCode::INTERNAL_ERROR
            ."  : internal error\n"
            ;
    }

    public function isHelp(): bool
    {
        return $this->help;
    }

    public function getSourceReader(): SourceReader
    {
        static $reader = new FileSourceReader($this->source);
        return $reader;
    }

    public function getInputReader(): InputReader
    {
        static $reader = new FileInputReader($this->input);
        return $reader;
    }

    public function getStdOutWriter(): OutputWriter
    {
        static $writer = new StreamWriter(STDOUT);
        return $writer;
    }

    public function getStdErrWriter(): OutputWriter
    {
        static $writer = new StreamWriter(STDERR);
        return $writer;
    }
}
