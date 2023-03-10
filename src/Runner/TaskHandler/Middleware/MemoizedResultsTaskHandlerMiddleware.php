<?php

declare(strict_types=1);

namespace PhpQa\Runner\TaskHandler\Middleware;

use function Amp\call;
use Amp\Promise;
use PhpQa\Runner\MemoizedTaskResultMap;
use PhpQa\Runner\TaskResult;
use PhpQa\Runner\TaskResultInterface;
use PhpQa\Runner\TaskRunnerContext;
use PhpQa\Task\TaskInterface;

class MemoizedResultsTaskHandlerMiddleware implements TaskHandlerMiddlewareInterface
{
    /**
     * @var MemoizedTaskResultMap
     */
    private $resultMap;

    public function __construct(MemoizedTaskResultMap $resultMap)
    {
        $this->resultMap = $resultMap;
    }

    public function handle(TaskInterface $task, TaskRunnerContext $runnerContext, callable $next): Promise
    {
        return call(
            /**
             * @return \Generator<mixed, Promise<TaskResultInterface>, mixed, TaskResultInterface>
             */
            function () use ($task, $runnerContext, $next) : \Generator {
                try {
                    /** @var TaskResultInterface $result */
                    $result = yield $next($task, $runnerContext);
                } catch (\Throwable $error) {
                    $result = TaskResult::createFailed($task, $runnerContext->getTaskContext(), $error->getMessage());
                }

                $this->resultMap->onResult($result);

                return $result;
            }
        );
    }
}
