<?php

declare(strict_types=1);

namespace PhpQa\Parser\Php\Context;

use PhpQa\Collection\ParseErrorsCollection;
use SplFileInfo;

class ParserContext
{
    /**
     * @var SplFileInfo
     */
    private $file;

    /**
     * @var ParseErrorsCollection
     */
    private $errors;

    /**
     * ParserContext constructor.
     */
    public function __construct(SplFileInfo $file, ParseErrorsCollection $errors)
    {
        $this->file = $file;
        $this->errors = $errors;
    }

    public function getFile(): SplFileInfo
    {
        return $this->file;
    }

    public function getErrors(): ParseErrorsCollection
    {
        return $this->errors;
    }
}
