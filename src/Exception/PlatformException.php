<?php

declare(strict_types=1);

namespace PhpQa\Exception;

use PhpQa\Util\Platform;
use Symfony\Component\Process\Process;

class PlatformException extends RuntimeException
{
    public static function commandLineStringLimit(Process $process): self
    {
        return new self(sprintf(
            'The Windows maximum amount of %s input characters exceeded while running process: %s ...',
            Platform::WINDOWS_COMMANDLINE_STRING_LIMITATION,
            substr($process->getCommandLine(), 0, 75)
        ));
    }
}
