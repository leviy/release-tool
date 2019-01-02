<?php
declare(strict_types=1);

namespace Leviy\ReleaseTool;

use Assert\Assertion;
use Leviy\ReleaseTool\Changelog\ChangelogGenerator;
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
     * @var ChangelogGenerator
     */
    private $changelogGenerator;

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
        ChangelogGenerator $changelogGenerator,
        array $actions
    ) {
        Assertion::allIsInstanceOf($actions, ReleaseAction::class);

        $this->versionControlSystem = $versionControlSystem;
        $this->versioningScheme = $versioningScheme;
        $this->actions = $actions;
        $this->changelogGenerator = $changelogGenerator;
    }

    public function getCurrentVersion(): string
    {
        return $this->versionControlSystem->getLastVersion();
    }

    public function release(string $versionString, InformationCollector $informationCollector): void
    {
        $changelog = $this->changelogGenerator->getChangelog();

        $this->versionControlSystem->createVersion($versionString);

        $question = 'A VCS tag has been created for version ' . $versionString . '. ';
        $question .= 'Do you want to push it to the remote repository and perform additional release steps?';

        if (!$informationCollector->askConfirmation($question)) {
            return;
        }

        $this->versionControlSystem->pushVersion($versionString);

        $version = $this->versioningScheme->getVersion($versionString);

        array_walk($this->actions, function (ReleaseAction $releaseAction) use ($version, $changelog): void {
            $releaseAction->execute($version, $changelog);
        });
    }

    public function determineNextVersion(InformationCollector $informationCollector): string
    {
        $current = $this->versionControlSystem->getLastVersion();
        $currentVersion = $this->versioningScheme->getVersion($current);

        return $this->versioningScheme->getNextVersion($currentVersion, $informationCollector)->getVersion();
    }

    public function determineNextPreReleaseVersion(InformationCollector $informationCollector): string
    {
        $current = $this->versionControlSystem->getLastVersion();
        $currentVersion = $this->versioningScheme->getVersion($current);

        return $this->versioningScheme->getNextPreReleaseVersion($currentVersion, $informationCollector)->getVersion();
    }

    public function isValidVersion(string $version): bool
    {
        return $this->versioningScheme->isValidVersion($version);
    }
}
