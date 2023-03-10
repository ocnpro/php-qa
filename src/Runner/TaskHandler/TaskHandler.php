<?php

declare(strict_types=1);

namespace PhpQa\Runner\TaskHandler;

use Amp\Promise;
use Amp\Success;
use PhpQa\Runner\TaskHandler\Middleware\TaskHandlerMiddlewareInterface;
use PhpQa\Runner\TaskResult;
use PhpQa\Runner\TaskResultInterface;
use PhpQa\Runner\TaskRunnerContext;
use PhpQa\Task\TaskInterface;

class TaskHandler
{
    /**
     * @var callable(TaskInterface, TaskRunnerContext): Promise<TaskResultInterface>
     * @var callable
     */
    private $stack;

    public function __construct(TaskHandlerMiddlewareInterface ...$handlers)
    {
        $this->stack = $this->createStack($handlers);
    }

    /**
     * Shortcut function to work directly with tagged services from the Symfony service container.
     * @param iterable<TaskHandlerMiddlewareInterface> $handlers
     */
    public static function fromIterable(iterable $handlers): self
    {
        return new self(
            ...($handlers instanceof \Traversable ? iterator_to_array($handlers) : $handlers)
        );
    }

    /**
     * @return Promise<TaskResultInterface>
     */
    public function handle(TaskInterface $task, TaskRunnerContext $runnerContext): Promise
    {
        return ($this->stack)($task, $runnerContext);
    }

    /**
     * @param TaskHandlerMiddlewareInterface[] $handlers
     * @return callable(TaskInterface, TaskRunnerContext): Promise<TaskResultInterface>
     */
    private function createStack(array $handlers): callable
    {
        $lastCallable = $this->fail();

        while ($handler = array_pop($handlers)) {
            $lastCallable = static function (
                TaskInterface $task,
                TaskRunnerContext $runnerContext
            ) use (
                $handler,
                $lastCallable
            ) : Promise {
                return $handler->handle($task, $runnerContext, $lastCallable);
            };
        }

        return $lastCallable;
    }

    /**
     * @return callable(TaskInterface, TaskRunnerContext): Promise<TaskResultInterface>
     */
    private function fail(): callable
    {
        return static function (TaskInterface $task, TaskRunnerContext $runnerContext): Promise {
            /** @var TaskResultInterface $result */
            $result = TaskResult::createFailed(
                $task,
                $runnerContext->getTaskContext(),
                'Task could not be handled by a task handler!'
            );

            return new Success($result);
        };
    }
}
