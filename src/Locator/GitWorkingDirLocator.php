<?php

declare(strict_types=1);

namespace PhpQa\Locator;

use PhpQa\Collection\ProcessArgumentsCollection;
use PhpQa\Exception\RuntimeException;
use PhpQa\Process\ProcessFactory;
use Symfony\Component\Process\ExecutableFinder;

class GitWorkingDirLocator
{
    /**
     * @var ExecutableFinder
     */
    private $executableFinder;

    public function __construct(ExecutableFinder $executableFinder)
    {
        $this->executableFinder = $executableFinder;
    }

    public function locate(): string
    {
        $arguments = ProcessArgumentsCollection::forExecutable((string) $this->executableFinder->find('git', 'git'));
        $arguments->add('rev-parse');
        $arguments->add('--show-toplevel');

        $process = ProcessFactory::fromArguments($arguments);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new RuntimeException(
                'The git directory could not be found. Did you initialize git? ('.$process->getErrorOutput().')'
            );
        }

        return trim($process->getOutput());
    }
}
