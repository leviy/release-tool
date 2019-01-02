<?php
declare(strict_types=1);

namespace Leviy\ReleaseTool\ReleaseAction;

use Leviy\ReleaseTool\Changelog\Changelog;
use Leviy\ReleaseTool\Changelog\Formatter\Formatter;
use Leviy\ReleaseTool\GitHub\GitHubClient;
use Leviy\ReleaseTool\Vcs\Git;
use Leviy\ReleaseTool\Versioning\Version;

final class GitHubReleaseAction implements ReleaseAction
{
    /**
     * @var GitHubClient
     */
    private $client;

    /**
     * @var Git
     */
    private $git;

    /**
     * @var Formatter
     */
    private $changelogFormatter;

    public function __construct(GitHubClient $client, Git $git, Formatter $changelogFormatter)
    {
        $this->client = $client;
        $this->git = $git;
        $this->changelogFormatter = $changelogFormatter;
    }

    public function execute(Version $version, Changelog $changelog): void
    {
        $tag = $this->git->getTagForVersion($version->getVersion());

        $body = $this->changelogFormatter->format($changelog);

        $this->client->createRelease($version, $tag, $body);
    }
}
