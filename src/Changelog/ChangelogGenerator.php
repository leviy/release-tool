<?php
declare(strict_types=1);

namespace Leviy\ReleaseTool\Changelog;

interface ChangelogGenerator
{
    /**
     * @return string[]
     */
    public function getChanges(): array;
}
