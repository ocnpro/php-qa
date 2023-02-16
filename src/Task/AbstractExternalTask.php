<?php

declare(strict_types=1);

namespace PhpQa\Task;

use PhpQa\Formatter\ProcessFormatterInterface;
use PhpQa\Process\ProcessBuilder;
use PhpQa\Task\Config\EmptyTaskConfig;
use PhpQa\Task\Config\TaskConfigInterface;

/**
 * @template-covariant Formatter extends ProcessFormatterInterface
 */
abstract class AbstractExternalTask implements TaskInterface
{
    /**
     * @var TaskConfigInterface
     */
    protected $config;

    /**
     * @var ProcessBuilder
     */
    protected $processBuilder;

    /**
     * @var Formatter
     */
    protected $formatter;

    /**
     * @param Formatter $formatter
     */
    public function __construct(ProcessBuilder $processBuilder, ProcessFormatterInterface $formatter)
    {
        $this->config = new EmptyTaskConfig();
        $this->processBuilder = $processBuilder;
        $this->formatter = $formatter;
    }

    public function getConfig(): TaskConfigInterface
    {
        return $this->config;
    }

    public function withConfig(TaskConfigInterface $config): TaskInterface
    {
        $new = clone $this;
        $new->config = $config;

        return $new;
    }
}
