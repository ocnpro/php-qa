<?php

declare(strict_types=1);

namespace PhpQa\Locator;

use PhpQa\Util\Filesystem;

class GitRepositoryDirLocator
{
    /**
     * @var Filesystem
     */
    private $filesystem;

    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    /**
     * Resolves the path to the git repository directory (aka as .git).
     * For submodules, it parses the .git file and resolves to the .git/modules/[submodules] directory
     */
    public function locate(string $gitDir): string
    {
        if (!$this->filesystem->isFile($gitDir)) {
            return $gitDir;
        }

        $content = $this->filesystem->readPath($gitDir);
        if (!preg_match('/gitdir:\s+(\S+)/', $content, $matches)) {
            return $gitDir;
        }

        $gitRepositoryDir = $matches[1];

        if ($this->filesystem->isAbsolutePath($gitRepositoryDir)) {
            return $gitRepositoryDir;
        }

        return $this->filesystem->buildPath(
            dirname($gitDir),
            $gitRepositoryDir
        );
    }
}
