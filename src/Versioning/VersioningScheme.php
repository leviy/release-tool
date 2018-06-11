<?php
declare(strict_types=1);

namespace Leviy\ReleaseTool\Versioning;

use Leviy\ReleaseTool\Interaction\InformationCollector;

interface VersioningScheme
{
    public function getNextVersion(string $currentVersion, InformationCollector $informationCollector): string;
}
