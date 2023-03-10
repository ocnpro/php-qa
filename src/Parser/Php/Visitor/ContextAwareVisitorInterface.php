<?php

declare(strict_types=1);

namespace PhpQa\Parser\Php\Visitor;

use PhpQa\Parser\Php\Context\ParserContext;
use PhpParser\NodeVisitor;

interface ContextAwareVisitorInterface extends NodeVisitor
{
    public function setContext(ParserContext $context): void;
}
