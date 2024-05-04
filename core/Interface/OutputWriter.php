<?php
/**
 * IPP - PHP Project Core
 * @author Radim Kocman
 * @author Zbyněk Křivka
 * 
 * DO NOT MODIFY THIS FILE!
 */

namespace IPP\Core\Interface;

/**
 * Common interface for output writers
 */
interface OutputWriter
{
    public function writeString(string $value): void;
    public function writeInt(int $value): void;
    public function writeBool(bool $value): void;

    public function writeFloat(float $value): void;
}
