<?php

declare(strict_types=1);

namespace PhpQa\Runner\Middleware;

use PhpQa\Collection\TaskResultCollection;
use PhpQa\Runner\TaskRunnerContext;

interface RunnerMiddlewareInterface
{
    /**
     * @param callable(TaskRunnerContext $info): TaskResultCollection $next
     */
    public function handle(TaskRunnerContext $context, callable $next): TaskResultCollection;
}
