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
 * PhpMnd task.
 */
class PhpMnd extends AbstractExternalTask
{
    public static function getConfigurableOptions(): OptionsResolver
    {
        $resolver = new OptionsResolver();
        $resolver->setDefaults([
            'directory' => '.',
            'whitelist_patterns' => [],
            'exclude' => [],
            'exclude_name' => [],
            'exclude_path' => [],
            'extensions' => [],
            'hint' => false,
            'ignore_funcs' => [],
            'ignore_numbers' => [],
            'ignore_strings' => [],
            'strings' => false,
            'triggered_by' => ['php'],
        ]);

        $resolver->addAllowedTypes('directory', ['string']);
        $resolver->addAllowedTypes('whitelist_patterns', ['array']);
        $resolver->addAllowedTypes('exclude', ['array']);
        $resolver->addAllowedTypes('exclude_name', ['array']);
        $resolver->addAllowedTypes('exclude_path', ['array']);
        $resolver->addAllowedTypes('extensions', ['array']);
        $resolver->addAllowedTypes('hint', ['bool']);
        $resolver->addAllowedTypes('ignore_funcs', ['array']);
        $resolver->addAllowedTypes('ignore_numbers', ['array']);
        $resolver->addAllowedTypes('ignore_strings', ['array']);
        $resolver->addAllowedTypes('strings', ['bool']);
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
        /** @var array $config */
        $config = $this->getConfig()->getOptions();
        /** @var array $whitelistPatterns */
        $whitelistPatterns = $config['whitelist_patterns'];
        /** @var array $extensions */
        $extensions = $config['triggered_by'];

        /** @var \PhpQa\Collection\FilesCollection $files */
        $files = $context->getFiles();
        if (0 !== \count($whitelistPatterns)) {
            $files = $files->paths($whitelistPatterns);
        }
        $files = $files->extensions($extensions);

        if (0 === \count($files)) {
            return TaskResult::createSkipped($this, $context);
        }

        $arguments = $this->processBuilder->createArgumentsForCommand('phpmnd');
        $arguments->addArgumentArray('--exclude=%s', $config['exclude']);
        $arguments->addArgumentArray('--exclude-file=%s', $config['exclude_name']);
        $arguments->addArgumentArray('--exclude-path=%s', $config['exclude_path']);
        $arguments->addOptionalCommaSeparatedArgument('--extensions=%s', $config['extensions']);
        $arguments->addOptionalArgument('--hint', $config['hint']);
        $arguments->addOptionalCommaSeparatedArgument('--ignore-funcs=%s', $config['ignore_funcs']);
        $arguments->addOptionalCommaSeparatedArgument('--ignore-numbers=%s', $config['ignore_numbers']);
        $arguments->addOptionalCommaSeparatedArgument('--ignore-strings=%s', $config['ignore_strings']);
        $arguments->addOptionalArgument('--strings', $config['strings']);
        $arguments->addOptionalCommaSeparatedArgument('--suffixes=%s', $config['triggered_by']);
        $arguments->add($config['directory']);

        $process = $this->processBuilder->buildProcess($arguments);
        $process->run();

        if (!$process->isSuccessful()) {
            return TaskResult::createFailed($this, $context, $this->formatter->format($process));
        }

        return TaskResult::createPassed($this, $context);
    }
}
