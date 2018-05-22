<?php
declare(strict_types=1);

namespace Leviy\ReleaseTool\Versioning;

use Leviy\ReleaseTool\Interaction\InformationCollector;

interface Strategy
{
    public function getNextVersion(string $currentVersion, InformationCollector $informationCollector): string;
}
