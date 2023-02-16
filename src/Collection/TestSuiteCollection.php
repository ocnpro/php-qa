<?php

declare(strict_types=1);

namespace PhpQa\Collection;

use Doctrine\Common\Collections\ArrayCollection;
use PhpQa\Exception\InvalidArgumentException;
use PhpQa\TestSuite\TestSuiteInterface;

/**
 * @extends ArrayCollection<string, TestSuiteInterface>
 */
class TestSuiteCollection extends ArrayCollection
{
    public function getRequired(string $name): TestSuiteInterface
    {
        if (!$result = $this->get($name)) {
            throw InvalidArgumentException::unknownTestSuite($name);
        }

        return $result;
    }

    public function getOptional(string $name): ?TestSuiteInterface
    {
        return $this->get($name);
    }
}
