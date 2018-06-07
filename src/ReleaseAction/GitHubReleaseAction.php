<?php
declare(strict_types=1);

namespace Leviy\ReleaseTool\ReleaseAction;

use Leviy\ReleaseTool\GitHub\GitHubClient;
use Leviy\ReleaseTool\Vcs\Git;

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

    public function __construct(GitHubClient $client, Git $git)
    {
        $this->client = $client;
        $this->git = $git;
    }

    public function execute(string $version): void
    {
        $tag = $this->git->getTagForVersion($version);

        $this->client->createRelease($version, $tag);
    }
}
