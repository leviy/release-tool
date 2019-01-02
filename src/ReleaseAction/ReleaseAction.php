<?php
declare(strict_types=1);

namespace Leviy\ReleaseTool\ReleaseAction;

use Leviy\ReleaseTool\Changelog\Changelog;
use Leviy\ReleaseTool\Versioning\Version;

interface ReleaseAction
{
    public function execute(Version $version, Changelog $changelog): void;
}
