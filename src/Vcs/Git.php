<?php
declare(strict_types=1);

namespace Leviy\ReleaseTool\Vcs;

use function array_filter;
use function array_map;
use function exec;
use function explode;
use function implode;
use function sprintf;
use function strlen;
use function substr;
use function trim;
use const PHP_EOL;

final class Git implements VersionControlSystem
{
    private const VERSION_GLOB = '[0-9]*';

    private const REMOTE = 'origin';

    /**
     * @var string
     */
    private $tagPrefix;

    public function __construct(string $tagPrefix = '')
    {
        $this->tagPrefix = $tagPrefix;
    }

    /**
     * @param string[] $arguments
     *
     * @return string[]
     *
     * @internal
     */
    public static function execute(string $command, array $arguments = []): array
    {
        $command = sprintf('git %s %s', $command, implode(' ', $arguments));

        exec($command . ' 2>&1', $output, $exitCode);

        if ($exitCode > 0) {
            throw new GitException(implode(PHP_EOL, $output));
        }

        return $output;
    }

    public function getLastVersion(): string
    {
        try {
            $tag = self::execute(
                'describe',
                [
                    '--abbrev=0',
                    sprintf('--match "%s%s"', $this->tagPrefix, self::VERSION_GLOB),
                ]
            )[0];
        } catch (GitException $exception) {
            throw new ReleaseNotFoundException('No release could be found', 0, $exception);
        }

        return $this->getVersionFromTag($tag);
    }

    public function createVersion(string $version): void
    {
        self::execute(
            'tag',
            [
                '--annotate',
                $this->tagPrefix . $version,
                sprintf('--message="Release %s"', $version),
            ]
        );
    }

    public function pushVersion(string $version): void
    {
        self::execute(
            'push',
            [
                self::REMOTE,
                'refs/tags/' . $this->tagPrefix . $version,
            ]
        );
    }

    /**
     * @return Commit[]
     */
    public function getCommitsSinceLastVersion(?string $pattern = null): array
    {
        try {
            $arguments = [
                $this->getTagForVersion($this->getLastVersion()) . '..HEAD',
            ];
        } catch (ReleaseNotFoundException $exception) {
            $arguments = [];
        }

        $arguments[] = '--format="%s%x1F%b%x1E"';

        if ($pattern !== null) {
            $arguments[] = '--grep="' . $pattern . '"';
            $arguments[] = '--extended-regexp';
        }

        $output = self::execute('log', $arguments);
        $output = implode("\n", $output);
        $commits = explode("\x1E", $output);

        $commits = array_filter($commits);

        $commits = array_map(
            function (string $commit): Commit {
                [$title, $body] = explode("\x1F", trim($commit));

                return new Commit($title, $body);
            },
            $commits
        );

        return $commits;
    }

    public function getTagForVersion(string $version): string
    {
        return $this->tagPrefix . $version;
    }

    private function getVersionFromTag(string $tag): string
    {
        return substr($tag, strlen($this->tagPrefix));
    }
}
