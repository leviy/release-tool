<?php
declare(strict_types=1);

namespace Leviy\ReleaseTool\Vcs;

interface VersionControlSystem
{
    public function getLastVersion(): string;

    public function findLastVersion(): ?string;

    public function createVersion(string $version): void;

    public function pushVersion(string $version): void;

    /**
     * @return Commit[]
     */
    public function getCommitsSinceLastVersion(?string $pattern = null): array;

    /**
     * @return Commit[]
     */
    public function getCommitsForVersion(string $version, ?string $pattern = null): array;

    public function getTagForVersion(string $version): string;

    /**
     * @return string[]
     */
    public function getPreReleasesForVersion(string $version): array;
}
