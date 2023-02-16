<?php

declare(strict_types=1);

namespace PhpQa\Parser;

use PhpQa\Collection\ParseErrorsCollection;
use SplFileInfo;

interface ParserInterface
{
    public function parse(SplFileInfo $file): ParseErrorsCollection;

    public function isInstalled(): bool;
}
