<?php
declare(strict_types=1);

namespace Leviy\ReleaseTool\ReleaseAction;

use Leviy\ReleaseTool\Host\GitHub\GitHubClient;

final class GitHubReleaseAction implements ReleaseAction
{
    /**
     * @var GitHubClient
     */
    private $client;

    public function __construct(GitHubClient $client)
    {
        $this->client = $client;
    }

    public function execute(string $version): void
    {
        $this->client->createRelease();
    }
}
