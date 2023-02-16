<?php

declare(strict_types=1);

namespace PhpQa\Task;

use PhpQa\Task\Context\ContextInterface;
use PhpQa\Task\Context\GitPreCommitContext;
use PhpQa\Task\Context\RunContext;
use PhpQa\Runner\TaskResult;
use PhpQa\Runner\TaskResultInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @extends AbstractParserTask<PhpParser>
 */
class PhpParser extends AbstractParserTask
{
    const KIND_PHP5 = 'php5';
    const KIND_PHP7 = 'php7';

    /**
     * @var \PhpQa\Parser\Php\PhpParser
     */
    protected $parser;

    public static function getConfigurableOptions(): OptionsResolver
    {
        $resolver = parent::getConfigurableOptions();

        $resolver->setDefaults([
            'triggered_by' => ['php'],
            'kind' => self::KIND_PHP7,
            'visitors' => [],
        ]);

        return $resolver;
    }

    public function canRunInContext(ContextInterface $context): bool
    {
        return $context instanceof GitPreCommitContext || $context instanceof RunContext;
    }

    public function run(ContextInterface $context): TaskResultInterface
    {
        $config = $this->getConfig()->getOptions();

        $files = $context->getFiles()->extensions($config['triggered_by']);
        if (0 === \count($files)) {
            return TaskResult::createSkipped($this, $context);
        }

        $this->parser->setParserOptions($config);

        $parseErrors = $this->parse($files);

        if ($parseErrors->count()) {
            return TaskResult::createFailed(
                $this,
                $context,
                sprintf(
                    "Some errors occured while parsing your PHP files:\n%s",
                    $parseErrors->__toString()
                )
            );
        }

        return TaskResult::createPassed($this, $context);
    }
}
