<?php
declare(strict_types=1);

namespace Leviy\ReleaseTool\ReleaseAction;

use Leviy\ReleaseTool\Changelog\ChangelogGenerator;
use Leviy\ReleaseTool\Changelog\Formatter\Formatter;
use Leviy\ReleaseTool\GitHub\GitHubClient;
use Leviy\ReleaseTool\Vcs\Git;
use Leviy\ReleaseTool\Versioning\Version;

final class GitHubReleaseAction implements ReleaseAction
{
    /**
     * @var ChangelogGenerator
     */
    private $changelogGenerator;

    /**
     * @var Formatter
     */
    private $changelogFormatter;

    /**
     * @var Git
     */
    private $git;

    /**
     * @var GitHubClient
     */
    private $client;

    public function __construct(
        ChangelogGenerator $changelogGenerator,
        Formatter $changelogFormatter,
        Git $git,
        GitHubClient $client
    ) {
        $this->changelogGenerator = $changelogGenerator;
        $this->changelogFormatter = $changelogFormatter;
        $this->git = $git;
        $this->client = $client;
    }

    public function execute(Version $version): void
    {
        $changelog = $this->changelogGenerator->getChangelogForVersion($version);

        $body = $this->changelogFormatter->format($changelog);

        $tag = $this->git->getTagForVersion($version->getVersion());

        $this->client->createRelease($version, $tag, $body);
    }
}
