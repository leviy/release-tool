<?php
declare(strict_types=1);

namespace Leviy\ReleaseTool\Changelog;

use Leviy\ReleaseTool\Versioning\Version;

interface ChangelogGenerator
{
    public function getUnreleasedChangelog(): Changelog;

    public function getChangelogForVersion(Version $version): Changelog;
}
