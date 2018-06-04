<?php
declare(strict_types=1);

namespace Leviy\ReleaseTool\ReleaseAction;

use Leviy\ReleaseTool\GitHub\GitHubClient;
use Leviy\ReleaseTool\Vcs\VersionControlSystem;

final class GitHubReleaseAction implements ReleaseAction
{
    /**
     * @var GitHubClient
     */
    private $client;

    /**
     * @var VersionControlSystem
     */
    private $vcs;

    public function __construct(GitHubClient $client, VersionControlSystem $vcs)
    {
        $this->client = $client;
        $this->vcs = $vcs;
    }

    public function execute(string $version): void
    {
        $tag = $this->vcs->getTagForVersion($version);

        $this->client->createRelease($version, $tag);
    }
}
