<?php

declare(strict_types=1);

namespace PhpQa\Parser\Php\Visitor;

use PhpQa\Parser\ParseError;
use PhpQa\Parser\Php\Context\ParserContext;
use PhpQa\Parser\Php\PhpParserError;
use PhpParser\NodeVisitorAbstract;

/**
 * @psalm-suppress MissingConstructor
 */
class AbstractVisitor extends NodeVisitorAbstract implements ContextAwareVisitorInterface
{
    /**
     * @var ParserContext
     */
    protected $context;

    public function setContext(ParserContext $context): void
    {
        $this->context = $context;
    }

    protected function addError(string $message, int $line = -1, string $type = ParseError::TYPE_ERROR): void
    {
        $errors = $this->context->getErrors();
        $fileName = $this->context->getFile()->getPathname();
        $errors->add(new PhpParserError($type, $message, $fileName, $line));
    }
}
