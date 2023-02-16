<?php

declare(strict_types=1);

namespace PhpQa\Task\Context;

use PhpQa\Collection\FilesCollection;

class RunContext implements ContextInterface
{
    private $files;

    public function __construct(FilesCollection $files)
    {
        $this->files = $files;
    }

    public function getFiles(): FilesCollection
    {
        return $this->files;
    }
}
