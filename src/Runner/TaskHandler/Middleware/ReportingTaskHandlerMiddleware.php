<?php

declare(strict_types=1);

namespace PhpQa\Runner\TaskHandler\Middleware;

use function Amp\call;
use Amp\Promise;
use PhpQa\Runner\Reporting\TaskResultsReporter;
use PhpQa\Runner\TaskResultInterface;
use PhpQa\Runner\TaskRunnerContext;
use PhpQa\Task\TaskInterface;

class ReportingTaskHandlerMiddleware implements TaskHandlerMiddlewareInterface
{
    /**
     * @var TaskResultsReporter
     */
    private $reporter;

    public function __construct(TaskResultsReporter $reporter)
    {
        $this->reporter = $reporter;
    }

    public function handle(TaskInterface $task, TaskRunnerContext $runnerContext, callable $next): Promise
    {
        return call(
            /**
             * @return \Generator<mixed, Promise<TaskResultInterface>, mixed, TaskResultInterface>
             */
            function () use ($task, $runnerContext, $next) {
                /** @var TaskResultInterface $result */
                $result = yield $next($task, $runnerContext);

                $this->reporter->report($runnerContext);

                return $result;
            }
        );
    }
}
