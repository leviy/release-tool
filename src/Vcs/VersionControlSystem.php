<?php
declare(strict_types=1);

namespace Leviy\ReleaseTool\Vcs;

interface VersionControlSystem
{
    public function getLastVersion(): string;

    public function createVersion(string $version): void;

    public function pushVersion(string $version): void;

    /**
     * @param null|string $pattern
     *
     * @return Commit[]
     */
    public function getCommitsSinceLastVersion(?string $pattern = null): array;

    public function getTagForVersion(string $version): string;
}
