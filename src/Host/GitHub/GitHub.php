<?php
declare(strict_types=1);

namespace Leviy\ReleaseTool\Host\GitHub;

use Leviy\ReleaseTool\Configuration\GlobalConfiguration;
use Leviy\ReleaseTool\Host\Host;
use Leviy\ReleaseTool\Host\RequestClient;
use Symfony\Component\Console\Style\StyleInterface;

class GitHub implements Host
{
    /**
     * @var RequestClient
     */
    private $client;

    /**
     * @var GlobalConfiguration
     */
    private $configuration;

    /**
     * @var StyleInterface
     */
    private $style;

    public function __construct(RequestClient $client, GlobalConfiguration $configuration)
    {
        $this->client = $client;
        $this->configuration = $configuration;
    }

    public function setStyle(StyleInterface $style): void
    {
        $this->style = $style;
    }

    public function createRelease(string $version): bool
    {
        $repositoryInformation = $this->getRepositoryInformation();

        if (!$repositoryInformation) {
            return false;
        }

        $authenticationInformation = $this->getAuthenticationInformation();

        if (!$authenticationInformation) {
            return false;
        }

        // @TODO: Error when release request fails
        $this->client->createRelease($version, $authenticationInformation, $repositoryInformation);

        return true;
    }

    /**
     * @return string[]|bool
     */
    private function getRepositoryInformation()
    {
        $owner = $this->configuration->findByKey('owner', 'github');
        $name = 'ReleaseToolTest'; // $this->configuration->findByKey('repository', 'github');

        if (empty($owner) || empty($name)) {
            return false;
        }

        return ['owner' => $owner, 'name' => $name];
    }

    /**
     * @return string[]|bool
     */
    private function getAuthenticationInformation()
    {
        $apiKey = $this->configuration->findByKey('apiKey', 'github');

        $username = $this->style->ask('Please enter your GitHub username');

        if (empty($apiKey) || empty($username)) {
            return false;
        }

        return ['apiKey' => $apiKey, 'username' => $username];
    }
}
