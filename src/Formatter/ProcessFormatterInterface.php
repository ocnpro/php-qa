<?php

declare(strict_types=1);

namespace PhpQa\Formatter;

use Symfony\Component\Process\Process;

interface ProcessFormatterInterface
{
    public function format(Process $process): string;
}
