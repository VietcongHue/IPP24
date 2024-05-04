<?php
/**
 * IPP - PHP Project Core
 * @author Radim Kocman
 * @author Zbyněk Křivka
 * 
 * DO NOT MODIFY THIS FILE!
 */

namespace IPP\Core;

use IPP\Core\Exception\InputFileException;
use IPP\Core\Exception\NotImplementedException;
use IPP\Core\Interface\InputReader;

/**
 * Input reader that reads from a file
 */
class FileInputReader implements InputReader
{
    /** @var resource */
    protected $resource;

    public function __construct(protected string $file)
    {
        $resource = fopen($file, 'r');
        if ($resource === false) {
            throw new InputFileException('Cannot open the input file');
        }
        $this->resource = $resource;
    }

    public function readString(): ?string
    {
        $result = fgets($this->resource);
        return ($result === false)? null : rtrim($result, "\n\r");
    }

    public function readInt(): ?int
    {
        $result = $this->readString();
        if (is_null($result)) return null;
        return filter_var(
            $result, 
            FILTER_VALIDATE_INT, 
            FILTER_NULL_ON_FAILURE | FILTER_FLAG_ALLOW_OCTAL | FILTER_FLAG_ALLOW_HEX
        );
    }

    public function readBool(): ?bool
    {
        $result = $this->readString();
        if (is_null($result)) return null;
        return filter_var(
            $result,
            FILTER_VALIDATE_BOOL,
            FILTER_NULL_ON_FAILURE
        );
    }

    public function readFloat(): ?float
    {
        throw new NotImplementedException;
    }
}
