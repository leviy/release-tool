<?php
declare(strict_types=1);

namespace Leviy\ReleaseTool\Changelog;

use Leviy\ReleaseTool\Vcs\Commit;
use Leviy\ReleaseTool\Vcs\VersionControlSystem;
use function array_map;
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

    public function getChangelog(): Changelog
    {
        $commits = $this->versionControlSystem->getCommitsSinceLastVersion(self::PULL_REQUEST_PATTERN);

        return new Changelog(
            array_map(
                function (Commit $commit): string {
                    preg_match('/' . self::PULL_REQUEST_PATTERN . '/', $commit->title, $matches);

                    return sprintf('%s (pull request #%d)', $commit->body, $matches[1]);
                },
                $commits
            )
        );
    }
}
