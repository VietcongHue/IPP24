<?php
/**
 * IPP - PHP Project Core
 * @author Radim Kocman
 * @author Zbyněk Křivka
 * 
 * DO NOT MODIFY THIS FILE!
 */

namespace IPP\Core\Exception;

use Exception;
use Throwable;

/**
 * Base for IPP exceptions that are handled in \IPP\Core\Engine
 */
abstract class IPPException extends Exception
{
    public function __construct(string $message, int $code, ?Throwable $previous = null, private bool $showTrace = true)
    {
        parent::__construct($message, $code, $previous);
    }

    public function getReport(): string
    {
        if (!$this->showTrace) {
            return "Exception: {$this->getMessage()}\n";
        }

        $report = "";
        $curr = $this;
        do {
            if ($curr !== $this) $report .= "\nNext ";
            $report .= "Exception: {$curr->getMessage()}\n";
            $report .= "Thrown in: {$curr->getFile()}:{$curr->getLine()}\n";
            $report .= "Stack trace:\n{$curr->getTraceAsString()}\n";
        } while ($curr = $curr->getPrevious());
        return $report;
    }
}
