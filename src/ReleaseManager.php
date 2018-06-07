<?php
declare(strict_types=1);

namespace Leviy\ReleaseTool;

use Assert\Assertion;
use Leviy\ReleaseTool\Changelog\ChangelogGenerator;
use Leviy\ReleaseTool\Interaction\InformationCollector;
use Leviy\ReleaseTool\ReleaseAction\ReleaseAction;
use Leviy\ReleaseTool\Vcs\VersionControlSystem;
use Leviy\ReleaseTool\Versioning\Strategy;
use function array_walk;

class ReleaseManager
{
    /**
     * @var VersionControlSystem
     */
    private $versionControlSystem;

    /**
     * @var Strategy
     */
    private $versioningStrategy;

    /**
     * @var ChangelogGenerator
     */
    private $changelogGenerator;

    /**
     * @var ReleaseAction[]
     */
    private $actions;

    /**
     * @param VersionControlSystem $versionControlSystem
     * @param Strategy             $versioningStrategy
     * @param ChangelogGenerator   $changelogGenerator
     * @param ReleaseAction[]      $actions
     */
    public function __construct(
        VersionControlSystem $versionControlSystem,
        Strategy $versioningStrategy,
        ChangelogGenerator $changelogGenerator,
        array $actions
    ) {
        Assertion::allIsInstanceOf($actions, ReleaseAction::class);

        $this->versionControlSystem = $versionControlSystem;
        $this->versioningStrategy = $versioningStrategy;
        $this->actions = $actions;
        $this->changelogGenerator = $changelogGenerator;
    }

    public function getCurrentVersion(): string
    {
        return $this->versionControlSystem->getLastVersion();
    }

    public function release(string $version, InformationCollector $informationCollector): void
    {
        $changeset = $this->changelogGenerator->getChanges();

        $this->versionControlSystem->createVersion($version);

        $question = 'A VCS tag has been created for version ' . $version . '. ';
        $question .= 'Do you want to push it to the remote repository and perform additional release steps?';

        if (!$informationCollector->askConfirmation($question)) {
            return;
        }

        $this->versionControlSystem->pushVersion($version);

        array_walk($this->actions, function (ReleaseAction $releaseAction) use ($version, $changeset): void {
            $releaseAction->execute($version, $changeset);
        });
    }

    public function determineNextVersion(InformationCollector $informationCollector): string
    {
        $current = $this->versionControlSystem->getLastVersion();

        return $this->versioningStrategy->getNextVersion($current, $informationCollector);
    }
}
