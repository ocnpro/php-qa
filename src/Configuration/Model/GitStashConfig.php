<?php

declare(strict_types=1);

namespace PhpQa\Configuration\Model;

class GitStashConfig
{
    /**
     * @var bool
     */
    private $ignoreUnstagedChanges;

    public function __construct(bool $ignoreUnstagedChanges)
    {
        $this->ignoreUnstagedChanges = $ignoreUnstagedChanges;
    }

    public function ignoreUnstagedChanges(): bool
    {
        return $this->ignoreUnstagedChanges;
    }
}
