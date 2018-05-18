<?php
declare(strict_types=1);

namespace Leviy\ReleaseTool\Versioning;

use Leviy\ReleaseTool\Interaction\InformationCollector;

final class SemanticVersioning implements Strategy
{
    public function getNextVersion(string $currentVersion, InformationCollector $informationCollector): string
    {
        $version = SemanticVersion::createFromVersionString($currentVersion);

        if ($informationCollector->askConfirmation('Does this release contain backward incompatible changes?')) {
            return $version->incrementMajorVersion()->getVersion();
        }

        if ($informationCollector->askConfirmation('Does this release contain new features?')) {
            return $version->incrementMinorVersion()->getVersion();
        }

        return $version->incrementPatchVersion()->getVersion();
    }
}
