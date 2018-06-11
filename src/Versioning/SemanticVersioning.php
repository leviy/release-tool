<?php
declare(strict_types=1);

namespace Leviy\ReleaseTool\Versioning;

use InvalidArgumentException;
use Leviy\ReleaseTool\Interaction\InformationCollector;
use function explode;

final class SemanticVersioning implements VersioningScheme
{
    private const PRE_RELEASE_TYPES = [
        'alpha' => 'alpha',
        'beta' => 'beta',
        'rc' => 'release candidate',
    ];

    public function getVersion(string $version): Version
    {
        return SemanticVersion::createFromVersionString($version);
    }

    public function isValidVersion(string $version): bool
    {
        return SemanticVersion::isValid($version);
    }

    public function getNextVersion(Version $currentVersion, InformationCollector $informationCollector): Version
    {
        if (!$currentVersion instanceof SemanticVersion) {
            throw new InvalidArgumentException('Current version must be a SemanticVersion instance');
        }

        if ($currentVersion->isPreRelease()) {
            return $currentVersion->release();
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

        if ($currentVersion->isPreRelease()) {
            $currentPreReleaseType = explode('.', $currentVersion->getPreReleaseIdentifier())[0];

            $defaultPreReleaseType = self::PRE_RELEASE_TYPES[$currentPreReleaseType] ?? null;
        }

        $type = $informationCollector->askMultipleChoice(
            'What kind of pre-release do you want to release?',
            [
                'a' => 'alpha',
                'b' => 'beta',
                'rc' => 'release candidate',
            ],
            $defaultPreReleaseType ?? null
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
