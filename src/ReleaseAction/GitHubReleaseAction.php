<?php
declare(strict_types=1);

namespace Leviy\ReleaseTool\ReleaseAction;

use Leviy\ReleaseTool\Changelog\Changelog;
use Leviy\ReleaseTool\Changelog\Filter\Filter;
use Leviy\ReleaseTool\Changelog\Formatter\Formatter;
use Leviy\ReleaseTool\GitHub\GitHubClient;
use Leviy\ReleaseTool\Vcs\Git;
use Leviy\ReleaseTool\Versioning\Version;

final class GitHubReleaseAction implements ReleaseAction
{
    /**
     * @var Filter
     */
    private $changelogFilter;

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

    public function __construct(Filter $changelogFilter, Formatter $changelogFormatter, Git $git, GitHubClient $client)
    {
        $this->changelogFilter = $changelogFilter;
        $this->changelogFormatter = $changelogFormatter;
        $this->git = $git;
        $this->client = $client;
    }

    public function execute(Version $version, Changelog $changelog): void
    {
        $changelog = $this->changelogFilter->filter($changelog);

        $body = $this->changelogFormatter->format($changelog);

        $tag = $this->git->getTagForVersion($version->getVersion());

        $this->client->createRelease($version, $tag, $body);
    }
}
