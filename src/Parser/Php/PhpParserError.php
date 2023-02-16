<?php

declare(strict_types=1);

namespace PhpQa\Parser\Php;

use PhpQa\Parser\ParseError;
use PhpParser\Error;

class PhpParserError extends ParseError
{
    public static function fromParseException(Error $exception, string $filename): self
    {
        return new self(
            ParseError::TYPE_FATAL,
            $exception->getRawMessage(),
            $filename,
            $exception->getStartLine()
        );
    }
}
