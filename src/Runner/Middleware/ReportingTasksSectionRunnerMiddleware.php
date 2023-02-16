<?php

declare(strict_types=1);

namespace PhpQa\Runner\Middleware;

use PhpQa\Collection\TaskResultCollection;
use PhpQa\Runner\Reporting\TaskResultsReporter;
use PhpQa\Runner\TaskRunnerContext;

class ReportingTasksSectionRunnerMiddleware implements RunnerMiddlewareInterface
{
    /**
     * @var TaskResultsReporter
     */
    private $reporter;

    public function __construct(TaskResultsReporter $reporter)
    {
        $this->reporter = $reporter;
    }

    public function handle(TaskRunnerContext $context, callable $next): TaskResultCollection
    {
        return $this->reporter->runInSection(
            /**
             * @return TaskResultCollection
             */
            function () use ($context, $next): TaskResultCollection {
                $this->reporter->report($context);

                return $next($context);
            }
        );
    }
}
