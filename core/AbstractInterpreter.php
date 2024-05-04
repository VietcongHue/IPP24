<?php
/**
 * IPP - PHP Project Core
 * @author Radim Kocman
 * @author Zbyněk Křivka
 * 
 * DO NOT MODIFY THIS FILE!
 */

namespace IPP\Core;

use IPP\Core\Interface\InputReader;
use IPP\Core\Interface\OutputWriter;
use IPP\Core\Interface\SourceReader;

/**
 * Base abstract class for \IPP\Student\Interpreter that defines its constructor and required methods
 */
abstract class AbstractInterpreter
{
    protected SourceReader $source;
    protected InputReader $input;
    protected OutputWriter $stdout;
    protected OutputWriter $stderr;

    final public function __construct(protected Settings $settings)
    {
        $this->init();
    }

    protected function init(): void
    {
        $this->source = $this->settings->getSourceReader();
        $this->input = $this->settings->getInputReader();
        $this->stdout = $this->settings->getStdOutWriter();
        $this->stderr = $this->settings->getStdErrWriter();
    }

    abstract public function execute(): int;
}
