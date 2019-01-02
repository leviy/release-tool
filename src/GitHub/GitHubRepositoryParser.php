<?php
declare(strict_types=1);

namespace Leviy\ReleaseTool\GitHub;

use Assert\Assertion;
use function explode;
use function str_replace;

class GitHubRepositoryParser
{
    public function getOwner(string $url): string
    {
        return $this->parseUrl($url)[0];
    }

    public function getRepository(string $url): string
    {
        return $this->parseUrl($url)[1];
    }

    /**
     * @return string[]
     */
    private function parseUrl(string $url): array
    {
        Assertion::regex($url, '#git@github.com:(.*)/(.*).git#', 'Value "%s" is not a valid GitHub SSH URL.');

        $url = str_replace(['git@github.com:', '.git'], '', $url);

        return explode('/', $url);
    }
}
