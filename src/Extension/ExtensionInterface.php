<?php

declare(strict_types=1);

namespace PhpQa\Extension;

use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Interface ExtensionInterface is used for PhpQa extensions to interface
 * with PhpQa through the service container.
 */
interface ExtensionInterface
{
    public function load(ContainerBuilder $container): void;
}
