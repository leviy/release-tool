<?php
declare(strict_types=1);

namespace Leviy\ReleaseTool\GitHub;

use GuzzleHttp\ClientInterface;
use function json_encode;

class GitHubClient
{
    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @var string
     */
    private $owner;

    /**
     * @var string
     */
    private $repository;

    public function __construct(ClientInterface $client, string $owner, string $repository)
    {
        $this->client = $client;
        $this->owner = $owner;
        $this->repository = $repository;
    }

    public function createRelease(string $version, string $tag): void
    {
        $jsonRequestBody = json_encode([
            'tag_name' => $tag,
            'name' => $version,
            'body' => $version,
        ]);

        $this->client->request(
            'POST',
            'https://api.github.com/repos/' . $this->owner . '/' . $this->repository . '/releases',
            [
                'body' => $jsonRequestBody,
            ]
        );
    }
}
