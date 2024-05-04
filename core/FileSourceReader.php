<?php
/**
 * IPP - PHP Project Core
 * @author Radim Kocman
 * @author Zbyněk Křivka
 * 
 * DO NOT MODIFY THIS FILE!
 */

namespace IPP\Core;

use DOMDocument;
use IPP\Core\Exception\InputFileException;
use IPP\Core\Exception\XMLException;
use IPP\Core\Interface\SourceReader;

/**
 * Source reader that reads from a file
 */
class FileSourceReader implements SourceReader
{
    protected string $content;

    public function __construct(protected string $file)
    {
        $content = file_get_contents($file);
        if ($content === false) {
            throw new InputFileException("Cannot read the source file");
        }
        $this->content = $content;
    }

    public function getDOMDocument(): DOMDocument
    {
        $document = new DOMDocument;
        if (!$document->loadXML($this->content, LIBXML_PARSEHUGE)) {
            throw new XMLException;
        }
        return $document;
    }
}
