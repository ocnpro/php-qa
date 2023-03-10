<?php

declare(strict_types=1);

namespace PhpQa\Fixer\Provider;

use PhpQa\Runner\FixableTaskResult;
use PhpQa\Runner\TaskResultInterface;
use Symfony\Component\Process\Process;

class FixableProcessResultProvider
{
    /**
     * @param callable(): Process $fixerProcessBuilder
     */
    public static function provide(
        TaskResultInterface $taskResult,
        callable $fixerProcessBuilder,
        array $successExitCodes = [0]
    ): FixableTaskResult {
        $fixerProcess = $fixerProcessBuilder();
        /** @psalm-suppress RedundantConditionGivenDocblockType */
        assert($fixerProcess instanceof Process);

        $fixerCommand = $fixerProcess->getCommandLine();
        $fixerMessage = sprintf(
            '%sYou can fix errors by running the following command:%s',
            PHP_EOL . PHP_EOL,
            PHP_EOL . $fixerCommand
        );

        return new FixableTaskResult(
            $taskResult->withAppendedMessage($fixerMessage),
            FixableProcessProvider::provide($fixerCommand, $successExitCodes)
        );
    }
}
