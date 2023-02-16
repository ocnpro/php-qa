<?php

declare(strict_types=1);

namespace PhpQa\Task;

use PhpQa\Collection\ProcessArgumentsCollection;
use PhpQa\Fixer\Provider\FixableProcessProvider;
use PhpQa\Runner\FixableTaskResult;
use PhpQa\Runner\TaskResult;
use PhpQa\Runner\TaskResultInterface;
use PhpQa\Task\AbstractExternalTask;
use PhpQa\Task\Context\ContextInterface;
use PhpQa\Task\Context\GitPreCommitContext;
use PhpQa\Task\Context\RunContext;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Prettier extends AbstractExternalTask
{
    public static function getConfigurableOptions(): OptionsResolver
    {
        $resolver = new OptionsResolver();
        $resolver->setDefaults([
            // Task config options
            'bin' => null,
            'triggered_by' => ['css', 'scss', 'sass', 'less', 'sss'],
            'whitelist_patterns' => [],

            // Prettier native config options
            'config' => null,
            'config_basedir' => null,
            'ignore_path' => null,
        ]);

        // Task config options
        $resolver->addAllowedTypes('bin', ['null', 'string']);
        $resolver->addAllowedTypes('triggered_by', ['array']);
        $resolver->addAllowedTypes('whitelist_patterns', ['array']);

        // Prettier native config options
        $resolver->addAllowedTypes('config', ['null', 'string']);
        $resolver->addAllowedTypes('ignore_path', ['null', 'string']);

        return $resolver;
    }

    public function canRunInContext(ContextInterface $context): bool
    {
        return ($context instanceof GitPreCommitContext || $context instanceof RunContext);
    }

    public function run(ContextInterface $context): TaskResultInterface
    {
        $config = $this->getConfig()->getOptions();

        $files = $context
            ->getFiles()
            ->paths($config['whitelist_patterns'])
            ->extensions($config['triggered_by']);

        if (0 === \count($files)) {
            return TaskResult::createSkipped($this, $context);
        }

        $arguments = isset($config['bin'])
            ? ProcessArgumentsCollection::forExecutable($config['bin'])
            : $this->processBuilder->createArgumentsForCommand('prettier');

        $arguments->addOptionalArgument('--config=%s', $config['config']);
        $arguments->addOptionalArgument('--ignore-path=%s', $config['ignore_path']);
        $arguments->add('--check');
        $arguments->addFiles($files);

        $process = $this->processBuilder->buildProcess($arguments);
        $process->run();

        if (!$process->isSuccessful()) {
            $arguments->add('--write');
            $fixerCommand = $this->processBuilder
                ->buildProcess($arguments)
                ->getCommandLine();

            $message = sprintf(
                '%sYou can fix errors by running the following command:%s',
                $this->formatter->format($process) . PHP_EOL . PHP_EOL,
                PHP_EOL . $fixerCommand
              );

            return new FixableTaskResult(
                TaskResult::createFailed($this, $context, $message),
                FixableProcessProvider::provide($fixerCommand)
            );
        }

        return TaskResult::createPassed($this, $context);
    }
}
