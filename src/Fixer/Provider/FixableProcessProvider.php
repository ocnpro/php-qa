<?php

declare(strict_types=1);

namespace PhpQa\Fixer\Provider;

use PhpQa\Exception\FixerException;
use PhpQa\Fixer\FixResult;
use Laravel\SerializableClosure\SerializableClosure;
use Symfony\Component\Process\Process;

class FixableProcessProvider
{
    /**
     * @param int[] $successExitCodes
     *
     * @return callable(): FixResult
     */
    public static function provide(string $command, array $successExitCodes = [0]): callable
    {
        return new SerializableClosure(
            static function () use ($command, $successExitCodes): FixResult {
                $process = Process::fromShellCommandline($command);
                $process->run();

                if (!in_array($process->getExitCode(), $successExitCodes, true)) {
                    return FixResult::failed(FixerException::fromProcess($process));
                }

                return FixResult::success($process->getOutput());
            }
        );
    }
}
