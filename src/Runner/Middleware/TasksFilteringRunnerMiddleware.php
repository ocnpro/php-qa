<?php

declare(strict_types=1);

namespace PhpQa\Runner\Middleware;

use PhpQa\Collection\TaskResultCollection;
use PhpQa\Collection\TasksCollection;
use PhpQa\Runner\TaskRunnerContext;

class TasksFilteringRunnerMiddleware implements RunnerMiddlewareInterface
{
    public function handle(TaskRunnerContext $context, callable $next): TaskResultCollection
    {
        return $next(
            $context->withTasks(
                (new TasksCollection($context->getTasks()->toArray()))
                    ->filterByContext($context->getTaskContext())
                    ->filterByTestSuite($context->getTestSuite())
                    ->filterByTaskNames($context->getTaskNames())
            )
        );
    }
}
