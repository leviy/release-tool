<?php
declare(strict_types=1);

namespace Leviy\ReleaseTool\GitHub;

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
     * @param string $url
     *
     * @return string[]
     */
    private function parseUrl(string $url): array
    {
        $url = str_replace(['git@github.com:', '.git'], '', $url);

        return explode('/', $url);
    }
}
