<?php

declare(strict_types=1);

namespace PhpQa\Runner;

use PhpQa\Task\Context\ContextInterface;
use PhpQa\Task\TaskInterface;

/**
 * @psalm-readonly
 */
interface TaskResultInterface
{
    const SKIPPED = -100;
    const PASSED = 0;
    const NONBLOCKING_FAILED = 90;
    const FAILED = 99;

    public function getTask(): TaskInterface;

    public function getResultCode(): int;

    public function isPassed(): bool;

    public function hasFailed(): bool;

    public function isSkipped(): bool;

    public function isBlocking(): bool;

    public function getMessage(): string;

    public function getContext(): ContextInterface;

    public function withAppendedMessage(string $message): self;
}
