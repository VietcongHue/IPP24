<?php
/**
 * IPP - PHP Project Core
 * @author Radim Kocman
 * @author Zbyněk Křivka
 * 
 * DO NOT MODIFY THIS FILE!
 */

namespace IPP\Core;

use IPP\Core\Exception\ExecuteReturnCodeException;
use IPP\Core\Exception\IntegrationException;
use IPP\Core\Exception\IPPException;
use IPP\Core\Exception\NotImplementedException;
use Throwable;

/**
 * Engine guides the execution of the script and handles IPP exceptions.
 * 
 * Notes:
 * - class \IPP\Student\Interpreter is required and it has to be a subclass of \IPP\Core\AbstractInterpreter
 * - class \IPP\Student\Settings is optional and if it exists, it has to be a subclass of \IPP\Core\Settings
 * - method execute() of \IPP\Student\Interpreter has to return only valid IPPcode24 return codes (0-9)
 * - error return codes has to be raised as an exception that is a subclass of \IPP\Core\Exception\IPPException
 */
class Engine
{
    public function run(): int
    {
        try {

            $settings = $this->getSettings();
            $settings->processArgs();

            if ($settings->isHelp()) {
                fwrite(STDOUT, $settings->getHelpString());
                return ReturnCode::OK;
            }

            $interpreter = $this->getInterpreter($settings);
            $code = $interpreter->execute();

            if ($code < 0 || $code > 9) {
                throw new ExecuteReturnCodeException();
            }

            return $code;
            
        } catch (IPPException $e) {
            fwrite(STDERR, $e->getReport());
            echo $e->getCode();
            return $e->getCode();
        }
    }

    private function getSettings(): Settings
    {
        $class = "\IPP\Student\Settings";
        if (!class_exists($class)) {
            return new Settings;
        }
        if (!is_subclass_of($class, Settings::class)) {
            throw new IntegrationException("Invalid parent class in $class");
        }
        try {
            $settings = new $class;
        } catch (Throwable $e) {
            throw new IntegrationException("Invalid class constructor in $class", $e);
        }
        return $settings;
    }

    private function getInterpreter(Settings $settings): AbstractInterpreter
    {
        $class = "\IPP\Student\Interpreter";
        if (!class_exists($class)) {
            throw new NotImplementedException("Not implemented class $class");
        }
        if (!is_subclass_of($class, AbstractInterpreter::class)) {
            throw new IntegrationException("Invalid parent class in $class");
        }
        try {
            $interpreter = new $class($settings);
        } catch (IPPException $e) {
            throw $e;
        } catch (Throwable $e) {
            throw new IntegrationException("Invalid class constructor in $class", $e);
        }
        return $interpreter;
    }
}
