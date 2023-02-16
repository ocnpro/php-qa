<?php

declare(strict_types=1);

namespace PhpQa\Runner\Middleware;

use PhpQa\Collection\TaskResultCollection;
use PhpQa\Runner\Reporting\RunnerReporter;
use PhpQa\Runner\TaskRunnerContext;

class ReportingRunnerMiddleware implements RunnerMiddlewareInterface
{
    /**
     * @var RunnerReporter
     */
    private $reporter;

    public function __construct(RunnerReporter $reporter)
    {
        $this->reporter = $reporter;
    }

    public function handle(TaskRunnerContext $context, callable $next): TaskResultCollection
    {
        $this->reporter->start($context);
        $results = $next($context);
        $this->reporter->finish($context, $results);

        return $results;
    }
}
