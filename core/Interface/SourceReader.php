<?php
/**
 * IPP - PHP Project Core
 * @author Radim Kocman
 * @author Zbyněk Křivka
 * 
 * DO NOT MODIFY THIS FILE!
 */

namespace IPP\Core\Interface;

use DOMDocument;

/**
 * Common interface for source readers
 */
interface SourceReader
{
    public function getDOMDocument(): DOMDocument;
}
