<?php
declare(strict_types=1);

namespace Leviy\ReleaseTool\Vcs;

interface VersionControlSystem
{
    public function tag(string $tag): void;
}
