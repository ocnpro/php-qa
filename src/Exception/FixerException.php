<?php

declare(strict_types=1);

namespace PhpQa\Exception;

use PhpQa\Formatter\RawProcessFormatter;
use Symfony\Component\Process\Process;

class FixerException extends RuntimeException
{
    public static function fromProcess(Process $process): self
    {
        return new self(
            'Error while fixing: '.
            $process->getCommandLine()
            . PHP_EOL
            . (new RawProcessFormatter())->format($process)
        );
    }
}
