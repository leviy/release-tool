<?php
declare(strict_types=1);

namespace Leviy\ReleaseTool\Versioning;

use InvalidArgumentException;
use Leviy\ReleaseTool\Interaction\InformationCollector;

final class SemanticVersioning implements VersioningScheme
{
    public function getVersion(string $version): Version
    {
        return SemanticVersion::createFromVersionString($version);
    }

    public function getNextVersion(Version $currentVersion, InformationCollector $informationCollector): Version
    {
        if (!$currentVersion instanceof SemanticVersion) {
            throw new InvalidArgumentException('Current version must be a SemanticVersion instance');
        }

        if ($informationCollector->askConfirmation('Does this release contain backward incompatible changes?')) {
            return $currentVersion->incrementMajorVersion();
        }

        if ($informationCollector->askConfirmation('Does this release contain new features?')) {
            return $currentVersion->incrementMinorVersion();
        }

        return $currentVersion->incrementPatchVersion();
    }

    public function getNextPreReleaseVersion(
        Version $currentVersion,
        InformationCollector $informationCollector
    ): Version {
        if (!$currentVersion->isPreRelease()) {
            $currentVersion = $this->getNextVersion($currentVersion, $informationCollector);
        }

        if (!$currentVersion instanceof SemanticVersion) {
            throw new InvalidArgumentException('Current version must be a SemanticVersion instance');
        }

        $type = $informationCollector->askMultipleChoice(
            'What kind of pre-release do you want to release?',
            [
                'a' => 'Alpha',
                'b' => 'Beta',
                'rc' => 'Release Candidate',
            ]
        );

        if ($type === 'a') {
            return $currentVersion->createAlphaRelease();
        }

        if ($type === 'b') {
            return $currentVersion->createBetaRelease();
        }

        return $currentVersion->createReleaseCandidate();
    }
}
