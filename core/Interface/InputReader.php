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
 * Common interface for input readers
 */
interface InputReader
{
    public function readString(): ?string;
    public function readInt(): ?int;
    public function readBool(): ?bool;

    public function readFloat(): ?float;
}
