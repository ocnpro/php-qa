<?php

declare(strict_types=1);

namespace PhpQa\Task\Context;

use PhpQa\Collection\FilesCollection;

interface ContextInterface
{
    public function getFiles(): FilesCollection;
}
