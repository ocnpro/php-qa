<?php

declare(strict_types=1);

namespace PhpQa\Linter;

use PhpQa\Collection\LintErrorsCollection;
use SplFileInfo;

interface LinterInterface
{
    public function lint(SplFileInfo $file): LintErrorsCollection;

    public function isInstalled(): bool;
}
