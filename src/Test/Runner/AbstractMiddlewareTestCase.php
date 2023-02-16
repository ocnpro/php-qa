<?php

declare(strict_types=1);

namespace PhpQa\Test\Runner;

use PhpQa\Collection\FilesCollection;
use PhpQa\IO\IOInterface;
use PhpQa\Runner\TaskRunnerContext;
use PhpQa\Task\Config\Metadata;
use PhpQa\Task\Config\TaskConfig;
use PhpQa\Task\Context\ContextInterface;
use PhpQa\Task\Context\RunContext;
use PhpQa\Task\TaskInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\Console\Style\StyleInterface;

class AbstractMiddlewareTestCase extends TestCase
{
    protected function createRunnerContext(): TaskRunnerContext
    {
        return new TaskRunnerContext(
            new RunContext(new FilesCollection())
        );
    }

    protected function createNextShouldNotBeCalledCallback(): callable
    {
        return static function () {
            throw new \RuntimeException('Expected next not to be called!');
        };
    }

    protected function mockIO(): IOInterface
    {
        /** @var ObjectProphecy|IOInterface $IO */
        $IO = $this->prophesize(IOInterface::class);
        $IO->isVerbose()->willReturn(false);
        $IO->style()->willReturn($this->prophesize(StyleInterface::class)->reveal());

        return $IO->reveal();
    }

    protected function mockTask(string $name, array $meta = []): TaskInterface
    {
        /** @var ObjectProphecy|TaskInterface $task */
        $task = $this->prophesize(TaskInterface::class);
        $task->getConfig()->willReturn(new TaskConfig($name, [], new Metadata($meta)));

        return $task->reveal();
    }
}
