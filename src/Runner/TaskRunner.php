<?php

declare(strict_types=1);

namespace PhpQa\Runner;

use PhpQa\Collection\TaskResultCollection;
use PhpQa\Collection\TasksCollection;

class TaskRunner
{
    /**
     * @var TasksCollection
     */
    private $tasks;

    /**
     * @var MiddlewareStack
     */
    private $middleware;

    public function __construct(TasksCollection $tasks, MiddlewareStack $middleware)
    {
        $this->tasks = $tasks;
        $this->middleware = $middleware;
    }

    public function run(TaskRunnerContext $runnerContext): TaskResultCollection
    {
        return $this->middleware->handle(
            $runnerContext->withTasks($this->tasks)
        );
    }
}
