<?php
declare(strict_types=1);

namespace Leviy\ReleaseTool\GitHub;

use GuzzleHttp\ClientInterface;
use Leviy\ReleaseTool\Vcs\Git;

class GitHubClientFactory
{
    /**
     * @var GitHubRepositoryParser
     */
    private $parser;

    /**
     * @var ClientInterface
     */
    private $client;

    public function __construct(GitHubRepositoryParser $parser, ClientInterface $client)
    {
        $this->parser = $parser;
        $this->client = $client;
    }

    public function createClient(): GitHubClient
    {
        $url = Git::execute('remote get-url origin')[0];

        $owner = $this->parser->getOwner($url);
        $repository = $this->parser->getRepository($url);

        return new GitHubClient($this->client, $owner, $repository);
    }
}
