<?php

declare(strict_types=1);

namespace PhpQa\Runner\Middleware;

use PhpQa\Collection\TaskResultCollection;
use PhpQa\Configuration\Model\RunnerConfig;
use PhpQa\IO\IOInterface;
use PhpQa\Runner\TaskRunnerContext;

class GroupByPriorityMiddleware implements RunnerMiddlewareInterface
{
    /**
     * @var IOInterface
     */
    private $IO;

    /**
     * @var RunnerConfig
     */
    private $config;

    public function __construct(IOInterface $IO, RunnerConfig $config)
    {
        $this->IO = $IO;
        $this->config = $config;
    }

    public function handle(TaskRunnerContext $context, callable $next): TaskResultCollection
    {
        $results = new TaskResultCollection();
        $grouped = $context->getTasks()
           ->sortByPriority()
           ->groupByPriority();

        foreach ($grouped as $priority => $tasks) {
            $this->IO->style()->title('Running tasks with priority '.$priority.'!');
            $results = new TaskResultCollection(array_merge(
                $results->toArray(),
                $next($context->withTasks($tasks))->toArray()
            ));

            // Stop on failure:
            if ($this->config->stopOnFailure() && $results->isFailed()) {
                return $results;
            }
        }

        return $results;
    }
}
