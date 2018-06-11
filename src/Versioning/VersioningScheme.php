<?php
declare(strict_types=1);

namespace Leviy\ReleaseTool\Versioning;

use Leviy\ReleaseTool\Interaction\InformationCollector;

interface VersioningScheme
{
    public function getVersion(string $version): Version;

    public function getNextVersion(Version $currentVersion, InformationCollector $informationCollector): Version;

    public function getNextPreReleaseVersion(
        Version $currentVersion,
        InformationCollector $informationCollector
    ): Version;
}
