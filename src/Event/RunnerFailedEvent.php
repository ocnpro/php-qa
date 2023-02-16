<?php

declare(strict_types=1);

namespace PhpQa\Event;

class RunnerFailedEvent extends RunnerEvent
{
    public function getMessages(): array
    {
        $messages = [];

        foreach ($this->getTaskResults() as $taskResult) {
            if ('' !== $taskResult->getMessage()) {
                $messages[] = $taskResult->getMessage();
            }
        }

        return $messages;
    }
}
