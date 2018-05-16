<?php
declare(strict_types=1);

namespace Leviy\ReleaseTool\Vcs;

interface VersionControlSystem
{
    public function getLastVersion(): string;

    public function createVersion(string $version): void;
}
