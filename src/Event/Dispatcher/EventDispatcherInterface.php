<?php

declare(strict_types=1);

namespace PhpQa\Event\Dispatcher;

use PhpQa\Event\Event;

interface EventDispatcherInterface
{
    public function dispatch(Event $event, string $name = null): void;
}
