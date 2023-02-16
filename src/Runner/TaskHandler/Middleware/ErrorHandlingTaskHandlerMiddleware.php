<?php

declare(strict_types=1);

namespace PhpQa\Runner\TaskHandler\Middleware;

use Amp\Parallel\Worker\TaskFailureException;
use function Amp\call;
use Amp\Promise;
use PhpQa\Exception\PlatformException;
use PhpQa\Runner\TaskResult;
use PhpQa\Runner\TaskResultInterface;
use PhpQa\Runner\TaskRunnerContext;
use PhpQa\Task\TaskInterface;

class ErrorHandlingTaskHandlerMiddleware implements TaskHandlerMiddlewareInterface
{
    public function handle(
        TaskInterface $task,
        TaskRunnerContext $runnerContext,
        callable $next
    ): Promise {
        return call(
            static function () use ($task, $runnerContext): TaskResultInterface {
                $taskContext = $runnerContext->getTaskContext();
                try {
                    $result = $task->run($taskContext);
                } catch (PlatformException $e) {
                    return TaskResult::createSkipped($task, $taskContext);
                } catch (\Throwable $e) {
                    return TaskResult::createFailed($task, $taskContext, $e->getMessage());
                }

                return $result;
            }
        );
    }
}
