<?php
declare(strict_types=1);

namespace Leviy\ReleaseTool\GitHub;

use function explode;
use function str_replace;
use function substr;

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
        if ($this->isHttpUrl($url)) {
            throw new InvalidUrlException('Remote must use SSH URL');
        }

        $url = str_replace(['git@github.com:', '.git'], '', $url);

        return explode('/', $url);
    }

    private function isHttpUrl(string $url): bool
    {
        return substr($url, 0, 4) === 'http';
    }
}
