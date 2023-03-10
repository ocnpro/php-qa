<?php

declare(strict_types=1);

namespace PhpQa\Parser\Php;

use PhpQa\Collection\ParseErrorsCollection;
use PhpQa\Parser\ParserInterface;
use PhpQa\Parser\Php\Context\ParserContext;
use PhpQa\Parser\Php\Factory\ParserFactory;
use PhpQa\Parser\Php\Factory\TraverserFactory;
use PhpQa\Util\Filesystem;
use PhpParser\Error;
use PhpParser\Parser;
use SplFileInfo;

class PhpParser implements ParserInterface
{
    /**
     * @var ParserFactory
     */
    private $parserFactory;

    /**
     * @var TraverserFactory
     */
    private $traverserFactory;

    /**
     * @var array
     */
    private $parserOptions = [];

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * PhpParser constructor.
     */
    public function __construct(
        ParserFactory $parserFactory,
        TraverserFactory $traverserFactory,
        Filesystem $filesystem
    ) {
        $this->parserFactory = $parserFactory;
        $this->traverserFactory = $traverserFactory;
        $this->filesystem = $filesystem;
    }

    public function setParserOptions(array $options): void
    {
        $this->parserOptions = $options;
    }

    public function parse(SplFileInfo $file): ParseErrorsCollection
    {
        $errors = new ParseErrorsCollection();
        $context = new ParserContext($file, $errors);
        $parser = $this->parserFactory->createFromOptions($this->parserOptions);
        $traverser = $this->traverserFactory->createForTaskContext($this->parserOptions, $context);

        try {
            $code = $this->filesystem->readFromFileInfo($file);
            $stmts = $parser->parse($code);
            $traverser->traverse((array) $stmts);
        } catch (Error $e) {
            $errors->add(PhpParserError::fromParseException($e, $file->getRealPath()));
        }

        return $errors;
    }

    public function isInstalled(): bool
    {
        return interface_exists(Parser::class);
    }
}
