<?php

declare(strict_types=1);

namespace PhpQa\Runner\TaskHandler\Middleware;

use Amp\Promise;
use PhpQa\Runner\TaskResultInterface;
use PhpQa\Runner\TaskRunnerContext;
use PhpQa\Task\TaskInterface;

interface TaskHandlerMiddlewareInterface
{
    /**
     * @param callable(TaskInterface, TaskRunnerContext): Promise<TaskResultInterface> $next
     * @return Promise<TaskResultInterface>
     */
    public function handle(
        TaskInterface $task,
        TaskRunnerContext $runnerContext,
        callable $next
    ): Promise;
}
