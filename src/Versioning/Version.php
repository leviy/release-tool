<?php
declare(strict_types=1);

namespace Leviy\ReleaseTool\Versioning;

interface Version
{
    public function getVersion(): string;

    public function isPreRelease(): bool;
}
