<?php

declare(strict_types=1);

namespace PhpQa\Task;

use PhpQa\Runner\TaskResult;
use PhpQa\Runner\TaskResultInterface;
use PhpQa\Task\Context\ContextInterface;
use PhpQa\Task\Context\GitPreCommitContext;
use PhpQa\Task\Context\RunContext;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Phing task.
 */
class Phing extends AbstractExternalTask
{
    public static function getConfigurableOptions(): OptionsResolver
    {
        $resolver = new OptionsResolver();
        $resolver->setDefaults([
            'build_file' => null,
            'task' => null,
            'triggered_by' => ['php'],
        ]);

        $resolver->addAllowedTypes('build_file', ['null', 'string']);
        $resolver->addAllowedTypes('task', ['null', 'string']);
        $resolver->addAllowedTypes('triggered_by', ['array']);

        return $resolver;
    }

    /**
     * {@inheritdoc}
     */
    public function canRunInContext(ContextInterface $context): bool
    {
        return $context instanceof GitPreCommitContext || $context instanceof RunContext;
    }

    /**
     * {@inheritdoc}
     */
    public function run(ContextInterface $context): TaskResultInterface
    {
        $config = $this->getConfig()->getOptions();
        $files = $context->getFiles()->extensions($config['triggered_by']);
        if (0 === \count($files)) {
            return TaskResult::createSkipped($this, $context);
        }

        $arguments = $this->processBuilder->createArgumentsForCommand('phing');
        $arguments->addOptionalArgument('-buildfile=%s', $config['build_file']);
        $arguments->addOptionalArgument('%s', $config['task']);

        $process = $this->processBuilder->buildProcess($arguments);
        $process->run();

        if (!$process->isSuccessful()) {
            return TaskResult::createFailed($this, $context, $this->formatter->format($process));
        }

        return TaskResult::createPassed($this, $context);
    }
}
