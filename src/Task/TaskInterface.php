<?php

declare(strict_types=1);

namespace PhpQa\Task;

use PhpQa\Runner\TaskResultInterface;
use PhpQa\Task\Config\TaskConfigInterface;
use PhpQa\Task\Context\ContextInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

interface TaskInterface
{
    public static function getConfigurableOptions(): OptionsResolver;

    public function canRunInContext(ContextInterface $context): bool;

    public function run(ContextInterface $context): TaskResultInterface;

    public function getConfig(): TaskConfigInterface;

    public function withConfig(TaskConfigInterface $config): TaskInterface;
}
