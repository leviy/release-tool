<?php
declare(strict_types=1);

namespace Leviy\ReleaseTool\ReleaseAction;

use Leviy\ReleaseTool\Changelog\Formatter\Formatter;
use Leviy\ReleaseTool\GitHub\GitHubClient;
use Leviy\ReleaseTool\Vcs\Git;
use Leviy\ReleaseTool\Versioning\Version;
use function implode;
use const PHP_EOL;

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

    /**
     * @inheritdoc
     */
    public function execute(Version $version, array $changeset): void
    {
        $tag = $this->git->getTagForVersion($version->getVersion());

        $body = implode(PHP_EOL, $this->changelogFormatter->formatChanges($changeset));

        $this->client->createRelease($version, $tag, $body);
    }
}
