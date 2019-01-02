<?php
declare(strict_types=1);

namespace Leviy\ReleaseTool\Changelog;

use function array_keys;
use function array_merge;

final class Changelog
{
    /**
     * @var string[][]
     */
    private $versions = [];

    /**
     * @var string[]
     */
    private $unreleasedChanges = [];

    /**
     * @return string[]
     */
    public function getVersions(): array
    {
        return array_keys($this->versions);
    }

    /**
     * @param string[] $changes
     */
    public function addVersion(string $version, array $changes): void
    {
        $this->versions[$version] = $changes;
    }

    /**
     * @return string[]
     */
    public function getChangesForVersion(string $version): array
    {
        return $this->versions[$version];
    }

    /**
     * @param string[] $unreleasedChanges
     */
    public function addUnreleasedChanges(array $unreleasedChanges): void
    {
        $this->unreleasedChanges = array_merge($this->unreleasedChanges, $unreleasedChanges);
    }

    /**
     * @return string[]
     */
    public function getUnreleasedChanges(): array
    {
        return $this->unreleasedChanges;
    }
}
