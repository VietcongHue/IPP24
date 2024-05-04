<?php
/**
 * IPP - PHP Project Core
 * @author Radim Kocman
 * @author Zbyněk Křivka
 * 
 * DO NOT MODIFY THIS FILE!
 */

namespace IPP\Core;

use IPP\Core\Exception\NotImplementedException;
use IPP\Core\Exception\OutputFileException;
use IPP\Core\Interface\OutputWriter;

/**
 * Output writer that writes to a stream
 */
class StreamWriter implements OutputWriter
{
    /** @param resource $stream */
    public function __construct(protected $stream)
    {}

    protected function checkResult(int|false $result): void
    {
        if ($result === false) {
            throw new OutputFileException("Cannot write to the output stream");
        }
    }

    public function writeString(string $value): void
    {
        $result = fwrite($this->stream, $value);
        $this->checkResult($result);
    }

    public function writeInt(int $value): void
    {
        $result = fwrite($this->stream, strval($value));
        $this->checkResult($result);
    }

    public function writeBool(bool $value): void
    {
        $result = fwrite($this->stream, $value? "true" : "false");
        $this->checkResult($result);
    }

    public function writeFloat(float $value): void
    {
        throw new NotImplementedException;
    }
}
