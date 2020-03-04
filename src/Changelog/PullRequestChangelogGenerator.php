<?php
declare(strict_types=1);

namespace Leviy\ReleaseTool\Changelog;

use Leviy\ReleaseTool\Vcs\Commit;
use Leviy\ReleaseTool\Vcs\VersionControlSystem;
use Leviy\ReleaseTool\Versioning\Version;
use function array_map;
use function array_reduce;
use function preg_match;
use function sprintf;

final class PullRequestChangelogGenerator implements ChangelogGenerator
{
    private const PULL_REQUEST_PATTERN = 'Merge pull request #([0-9]+) from .*';

    /**
     * @var VersionControlSystem
     */
    private $versionControlSystem;

    public function __construct(VersionControlSystem $versionControlSystem)
    {
        $this->versionControlSystem = $versionControlSystem;
    }

    public function getUnreleasedChangelog(): Changelog
    {
        $unreleasedCommits = $this->versionControlSystem->getCommitsSinceLastVersion(self::PULL_REQUEST_PATTERN);
        $unreleasedChanges = array_map([$this, 'createChangeFromCommit'], $unreleasedCommits);

        $changelog = new Changelog();
        $changelog->addUnreleasedChanges($unreleasedChanges);

        return $changelog;
    }

    public function getChangelogForVersion(Version $version): Changelog
    {
        $versions = [];
        if (!$version->isPreRelease()) {
            $versions = $this->versionControlSystem->getPreReleasesForVersion($version->getVersion());
        }

        $versions[] = $version->getVersion();

        return array_reduce(
            $versions,
            function (Changelog $changelog, string $version): Changelog {
                $commits = $this->versionControlSystem->getCommitsForVersion(
                    $version,
                    self::PULL_REQUEST_PATTERN
                );

                $changes = array_map(
                    [$this, 'createChangeFromCommit'],
                    $commits
                );

                $changelog->addVersion($version, $changes);

                return $changelog;
            },
            new Changelog()
        );
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod) Method is used as callable in getChangelog()
     */
    private function createChangeFromCommit(Commit $commit): string
    {
        $matches = [];

        preg_match('/' . self::PULL_REQUEST_PATTERN . '/', $commit->title, $matches);

        return sprintf('%s (pull request #%d)', $commit->body, $matches[1]);
    }
}
