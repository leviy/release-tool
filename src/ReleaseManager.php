<?php
declare(strict_types=1);

namespace Leviy\ReleaseTool;

use Assert\Assertion;
use Leviy\ReleaseTool\Interaction\InformationCollector;
use Leviy\ReleaseTool\ReleaseAction\ReleaseAction;
use Leviy\ReleaseTool\Vcs\VersionControlSystem;
use Leviy\ReleaseTool\Versioning\VersioningScheme;
use function array_walk;

class ReleaseManager
{
    /**
     * @var VersionControlSystem
     */
    private $versionControlSystem;

    /**
     * @var VersioningScheme
     */
    private $versioningScheme;

    /**
     * @var ReleaseAction[]
     */
    private $actions;

    /**
     * @param ReleaseAction[] $actions
     */
    public function __construct(
        VersionControlSystem $versionControlSystem,
        VersioningScheme $versioningScheme,
        array $actions
    ) {
        Assertion::allIsInstanceOf($actions, ReleaseAction::class);

        $this->versionControlSystem = $versionControlSystem;
        $this->versioningScheme = $versioningScheme;
        $this->actions = $actions;
    }

    public function getCurrentVersion(): string
    {
        return $this->versionControlSystem->getLastVersion();
    }

    public function hasVersions(): bool
    {
        return $this->versionControlSystem->findLastVersion() !== null;
    }

    public function release(string $versionString, InformationCollector $informationCollector): void
    {
        $this->versionControlSystem->createVersion($versionString);

        $question = 'A VCS tag has been created for version ' . $versionString . '. ';
        $question .= 'Do you want to push it to the remote repository and perform additional release steps?';

        if (!$informationCollector->askConfirmation($question)) {
            return;
        }

        $this->versionControlSystem->pushVersion($versionString);

        $version = $this->versioningScheme->getVersion($versionString);

        array_walk(
            $this->actions,
            function (ReleaseAction $releaseAction) use ($version): void {
                $releaseAction->execute($version);
            }
        );
    }

    public function determineNextVersion(InformationCollector $informationCollector): string
    {
        if ($this->versionControlSystem->findLastVersion() === null) {
            return '1.0.0';
        }

        $current = $this->versionControlSystem->getLastVersion();
        $currentVersion = $this->versioningScheme->getVersion($current);

        return $this->versioningScheme->getNextVersion($currentVersion, $informationCollector)->getVersion();
    }

    public function determineNextPreReleaseVersion(InformationCollector $informationCollector): string
    {
        if ($this->versionControlSystem->findLastVersion() === null) {
            $currentVersion = $this->versioningScheme->getVersion('0.0.0');

            return $this->versioningScheme
                ->getNextPreReleaseVersion($currentVersion, $informationCollector)
                ->getVersion();
        }

        $current = $this->versionControlSystem->getLastVersion();
        $currentVersion = $this->versioningScheme->getVersion($current);

        return $this->versioningScheme->getNextPreReleaseVersion($currentVersion, $informationCollector)->getVersion();
    }

    public function isValidVersion(string $version): bool
    {
        return $this->versioningScheme->isValidVersion($version);
    }
}
