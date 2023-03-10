<?php

declare(strict_types=1);

namespace PhpQa\Configuration\Model;

/**
 * @psalm-immutable
 */
class ParallelConfig
{
    /**
     * @var bool
     */
    private $enabled;

    /**
     * @var int
     */
    private $maxWorkers;

    public function __construct(
        bool $enabled,
        int $maxWorkers
    ) {
        $this->enabled = $enabled;
        $this->maxWorkers = $maxWorkers;
    }

    /**
     * @param array{max_workers: int, enabled: bool} $config
     */
    public static function fromArray(array $config): self
    {
        return new self(
            ($config['enabled'] ?? false),
            ($config['max_workers'] ?? 1)
        );
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function getMaxWorkers(): int
    {
        return $this->maxWorkers;
    }
}
