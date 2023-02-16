<?php

declare(strict_types=1);

namespace PhpQa\Runner\Parallel;

use Amp\Parallel\Worker\DefaultPool;
use Amp\Parallel\Worker\Pool;
use PhpQa\Configuration\Model\ParallelConfig;

class PoolFactory
{
    /**
     * @var ParallelConfig
     */
    private $config;

    public function __construct(ParallelConfig $config)
    {
        $this->config = $config;
    }

    public function create(): Pool
    {
        return new DefaultPool(
            $this->config->getMaxWorkers()
        );
    }
}
