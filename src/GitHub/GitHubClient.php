<?php
declare(strict_types=1);

namespace Leviy\ReleaseTool\Host\GitHub;

use GuzzleHttp\Client;

class GitHubClient
{
    /**
     * @var Client
     */
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @param string   $version
     * @param string[] $repositoryInformation
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function createRelease(string $version, array $repositoryInformation): void
    {
        $jsonRequestBody = $this->createReleaseRequestBody($version);

        $this->client->request(
            'POST',
            'https://api.github.com/repos/' . $repositoryInformation['owner'] . '/' . $repositoryInformation['name'] . '/releases',
            [
                'body' => $jsonRequestBody,
            ]
        );
    }

    private function createReleaseRequestBody(string $version): string
    {
        return json_encode([
            'tag_name' => $version,
            'name' => $version,
            'body' => $version
        ]);
    }
}
