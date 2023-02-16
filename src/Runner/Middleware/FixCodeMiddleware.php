<?php

declare(strict_types=1);

namespace PhpQa\Runner\Middleware;

use PhpQa\Collection\TaskResultCollection;
use PhpQa\Fixer\FixerUpper;
use PhpQa\Runner\TaskRunnerContext;

class FixCodeMiddleware implements RunnerMiddlewareInterface
{
    /**
     * @var FixerUpper
     */
    private $fixerUpper;

    public function __construct(FixerUpper $fixerUpper)
    {
        $this->fixerUpper = $fixerUpper;
    }

    public function handle(TaskRunnerContext $context, callable $next): TaskResultCollection
    {
        /** @var TaskResultCollection $results */
        $results = $next($context);

        $this->fixerUpper->fix($results);

        return $results;
    }
}
