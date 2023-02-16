<?php

declare(strict_types=1);

namespace PhpQa\Configuration\Configurator;

use PhpQa\Task\Config\TaskConfigInterface;
use PhpQa\Task\TaskInterface;

class TaskConfigurator
{
    public function __invoke(TaskInterface $task, TaskConfigInterface $config): TaskInterface
    {
        return $task->withConfig($config);
    }
}
