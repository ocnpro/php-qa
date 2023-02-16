<?php

declare(strict_types=1);

namespace PhpQa\Task;

use PhpQa\Collection\FilesCollection;
use PhpQa\Exception\RuntimeException;
use PhpQa\Linter\Yaml\YamlLinter;
use PhpQa\Runner\TaskResult;
use PhpQa\Runner\TaskResultInterface;
use PhpQa\Task\Context\ContextInterface;
use PhpQa\Task\Context\GitPreCommitContext;
use PhpQa\Task\Context\RunContext;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @extends AbstractLinterTask<YamlLinter>
 */
class YamlLint extends AbstractLinterTask
{

    /**
     * @var YamlLinter
     */
    protected $linter;

    public static function getConfigurableOptions(): OptionsResolver
    {
        $resolver = parent::getConfigurableOptions();
        $resolver->setDefaults([
            'object_support' => false,
            'exception_on_invalid_type' => false,
            'parse_constant' => false,
            'parse_custom_tags' => false,
            'whitelist_patterns' => [],
        ]);

        $resolver->addAllowedTypes('object_support', ['bool']);
        $resolver->addAllowedTypes('exception_on_invalid_type', ['bool']);
        $resolver->addAllowedTypes('parse_constant', ['bool']);
        $resolver->addAllowedTypes('parse_custom_tags', ['bool']);
        $resolver->addAllowedTypes('whitelist_patterns', ['array']);

        return $resolver;
    }

    public function canRunInContext(ContextInterface $context): bool
    {
        return $context instanceof GitPreCommitContext || $context instanceof RunContext;
    }

    public function run(ContextInterface $context): TaskResultInterface
    {
        /** @var array $config */
        $config = $this->getConfig()->getOptions();
        $whitelistPatterns = $config['whitelist_patterns'];
        $extensions = '/\.(yaml|yml)$/i';

        /** @var FilesCollection $files */
        $files = $context->getFiles()->name($extensions);
        if (\count($whitelistPatterns)) {
            $files = $context->getFiles()->paths($whitelistPatterns)->name($extensions);
        }
        if (0 === \count($files)) {
            return TaskResult::createSkipped($this, $context);
        }

        $this->linter->setObjectSupport($config['object_support']);
        $this->linter->setExceptionOnInvalidType($config['exception_on_invalid_type']);
        $this->linter->setParseCustomTags($config['parse_custom_tags']);
        $this->linter->setParseConstants($config['parse_constant']);

        try {
            $lintErrors = $this->lint($files);
        } catch (RuntimeException $e) {
            return TaskResult::createFailed($this, $context, $e->getMessage());
        }

        if ($lintErrors->count()) {
            return TaskResult::createFailed($this, $context, (string) $lintErrors);
        }

        return TaskResult::createPassed($this, $context);
    }
}
