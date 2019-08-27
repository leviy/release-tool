<?php
declare(strict_types=1);

namespace Leviy\ReleaseTool\Changelog\Formatter\Filter;

use function preg_replace;
use function sprintf;

final class GitHubPullRequestUrlFilter implements Filter
{
    private const PULL_REQUEST_URL = 'https://github.com/%s/pull/$1';

    /**
     * @var string
     */
    private $pullRequestUrl;

    public function __construct(string $repositorySlug)
    {
        $this->pullRequestUrl = sprintf(self::PULL_REQUEST_URL, $repositorySlug);
    }

    public function filter(string $line): string
    {
        return preg_replace(
            '/pull request #(\d+)/',
            'pull request [#$1](' . $this->pullRequestUrl . ')',
            $line
        ) ?: $line;
    }
}
