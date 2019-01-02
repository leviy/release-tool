<?php
declare(strict_types=1);

namespace Leviy\ReleaseTool\Changelog;

interface ChangelogGenerator
{
    public function getChangelog(): Changelog;
}
