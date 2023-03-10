<?php

namespace PhpQa\Task\Config;

interface TaskConfigInterface
{
    public function getName(): string;

    public function getOptions(): array;

    public function getMetadata(): Metadata;
}
