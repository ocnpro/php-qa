<?php

declare(strict_types=1);

namespace PhpQa\Parser\Php\Visitor;

use PhpParser\NodeVisitor;

interface ConfigurableVisitorInterface extends NodeVisitor
{
    public function configure(array $options): void;
}
