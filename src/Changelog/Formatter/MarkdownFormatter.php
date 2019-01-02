<?php
declare(strict_types=1);

namespace Leviy\ReleaseTool\Changelog\Formatter;

use Leviy\ReleaseTool\Changelog\Changelog;
use function array_map;
use function array_merge;
use function implode;
use function preg_replace;
use function sprintf;
use const PHP_EOL;

final class MarkdownFormatter implements Formatter
{
    private const PULL_REQUEST_URL = 'https://github.com/%s/pull/$1';

    /**
     * @var string
     */
    private $pullRequestUrl;

    /**
     * @var string
     */
    private $issueUrl;

    /**
     * @var string
     */
    private $issuePattern;

    public function __construct(string $repositorySlug, string $issuePattern = '', string $issueUrl = '')
    {
        $this->pullRequestUrl = sprintf(self::PULL_REQUEST_URL, $repositorySlug);
        $this->issueUrl = $issueUrl;
        $this->issuePattern = $issuePattern;
    }

    public function format(Changelog $changelog): string
    {
        $changes = array_map(
            function (string $line): string {
                return $this->formatLine($line);
            },
            $changelog->getChanges()
        );

        $lines = array_merge(
            [
                '# Changelog',
                '',
            ],
            $changes
        );

        return implode(PHP_EOL, $lines);
    }

    private function formatLine(string $line): string
    {
        $line = preg_replace(
            '/pull request #(\d+)/',
            'pull request [#$1](' . $this->pullRequestUrl . ')',
            $line
        );

        if (!empty($this->issuePattern) && !empty($this->issueUrl)) {
            $line = preg_replace(
                $this->issuePattern,
                '[$1](' . $this->issueUrl . ')',
                $line
            );
        }

        $line = '* ' . $line;

        return $line;
    }
}
