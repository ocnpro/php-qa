<?php

declare(strict_types=1);

namespace PhpQa\Event;

use PhpQa\Collection\TaskResultCollection;
use PhpQa\Collection\TasksCollection;
use PhpQa\Task\Context\ContextInterface;

class RunnerEvent extends Event
{
    /**
     * @var TasksCollection
     */
    private $tasks;

    /**
     * @var ContextInterface
     */
    private $context;

    /**
     * @var TaskResultCollection
     */
    private $taskResults;

    public function __construct(TasksCollection $tasks, ContextInterface $context, TaskResultCollection $taskResults)
    {
        $this->tasks = $tasks;
        $this->context = $context;
        $this->taskResults = $taskResults;
    }

    public function getTasks(): TasksCollection
    {
        return $this->tasks;
    }

    public function getContext(): ContextInterface
    {
        return $this->context;
    }

    public function getTaskResults(): TaskResultCollection
    {
        return $this->taskResults;
    }
}
