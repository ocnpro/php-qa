<?php

declare(strict_types=1);

namespace PhpQa\Runner;

use PhpQa\Fixer\FixResult;
use PhpQa\Task\Context\ContextInterface;
use PhpQa\Task\TaskInterface;

/**
 * @psalm-readonly
 */
class FixableTaskResult implements TaskResultInterface
{
    /**
     * @var TaskResultInterface
     */
    private $result;

    /**
     * @var callable(): FixResult
     */
    private $fixer;

    public function __construct(TaskResultInterface $result, callable $fixer)
    {
        $this->result = $result;
        $this->fixer = $fixer;
    }

    public function getTask(): TaskInterface
    {
        return $this->result->getTask();
    }

    public function getResultCode(): int
    {
        return $this->result->getResultCode();
    }

    public function isPassed(): bool
    {
        return $this->result->isPassed();
    }

    public function hasFailed(): bool
    {
        return $this->result->hasFailed();
    }

    public function isSkipped(): bool
    {
        return $this->result->isSkipped();
    }

    public function isBlocking(): bool
    {
        return $this->result->isBlocking();
    }

    public function getMessage(): string
    {
        return $this->result->getMessage();
    }

    public function getContext(): ContextInterface
    {
        return $this->result->getContext();
    }

    public function withAppendedMessage(string $message): TaskResultInterface
    {
        $new = clone $this;
        $new->result = $this->result->withAppendedMessage($message);

        return $new;
    }

    public function fix(): FixResult
    {
        return ($this->fixer)();
    }
}
