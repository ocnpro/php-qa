<?php

declare(strict_types=1);

namespace PhpQa\Runner\TaskHandler\Middleware;

use function Amp\call;
use Amp\Promise;
use PhpQa\Event\Dispatcher\EventDispatcherInterface;
use PhpQa\Event\TaskEvent;
use PhpQa\Event\TaskEvents;
use PhpQa\Event\TaskFailedEvent;
use PhpQa\Exception\RuntimeException;
use PhpQa\Runner\TaskResultInterface;
use PhpQa\Runner\TaskRunnerContext;
use PhpQa\Task\TaskInterface;

class EventDispatchingTaskHandlerMiddleware implements TaskHandlerMiddlewareInterface
{
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function handle(TaskInterface $task, TaskRunnerContext $runnerContext, callable $next): Promise
    {
        return call(
            /**
             * @return \Generator<mixed, Promise<TaskResultInterface>, mixed, TaskResultInterface>
             */
            function () use ($task, $runnerContext, $next): \Generator {
                $taskContext = $runnerContext->getTaskContext();
                $this->eventDispatcher->dispatch(new TaskEvent($task, $taskContext), TaskEvents::TASK_RUN);

                /** @var TaskResultInterface $result */
                $result = yield $next($task, $runnerContext);

                if ($result->isSkipped()) {
                    $this->eventDispatcher->dispatch(new TaskEvent($task, $taskContext), TaskEvents::TASK_SKIPPED);
                    return $result;
                }

                if ($result->hasFailed()) {
                    $e = new RuntimeException($result->getMessage());
                    $this->eventDispatcher->dispatch(
                        new TaskFailedEvent($task, $taskContext, $e),
                        TaskEvents::TASK_FAILED
                    );

                    return $result;
                }

                $this->eventDispatcher->dispatch(new TaskEvent($task, $taskContext), TaskEvents::TASK_COMPLETE);

                return $result;
            }
        );
    }
}
