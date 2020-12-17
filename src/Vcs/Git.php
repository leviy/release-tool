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
use function strpos;
use function substr;
use function trim;
use const PHP_EOL;

class Git implements VersionControlSystem
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
     * @return string[]
     *
     * @internal
     */
    public static function execute(string $command, array $arguments = []): array
    {
        $command = sprintf('git %s %s', $command, implode(' ', $arguments));

        $output = null;
        $exitCode = null;

        exec($command . ' 2>&1', $output, $exitCode);

        if ($exitCode > 0 && strpos(implode(' ', $output), 'not a git repository') > 0) {
            throw new RepositoryNotFoundException('Error: Repository not found in current path.');
        }

        if ($exitCode > 0) {
            throw new GitException(implode(PHP_EOL, $output));
        }

        return $output;
    }

    public function getLastVersion(): string
    {
        $tag = $this->describe();

        return $this->getVersionFromTag($tag);
    }

    public function findLastVersion(): ?string
    {
        try {
            $tag = $this->describe();
        } catch (ReleaseNotFoundException $exception) {
            return null;
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
            $range = $this->getTagForVersion($this->getLastVersion()) . '..HEAD';
        } catch (ReleaseNotFoundException $exception) {
            $range = 'HEAD';
        }

        return $this->getCommitsInRange($range, $pattern);
    }

    /**
     *
     * @return Commit[]
     */
    public function getCommitsForVersion(string $version, ?string $pattern = null): array
    {
        $revisionRange = $this->getRevisionRangeForVersion($version);

        return $this->getCommitsInRange($revisionRange, $pattern);
    }

    public function getTagForVersion(string $version): string
    {
        return $this->tagPrefix . $version;
    }

    /**
     * @return string[]
     *
     * TODO: this method is based on the assumption that semantic versioning is used. What if we use a different
     * versioning system?
     */
    public function getPreReleasesForVersion(string $version): array
    {
        $tag = $this->getTagForVersion($version);

        $preReleaseTagPattern = sprintf('%s-*', $tag);

        $tags = self::execute('tag', ['--list', '--sort=taggerdate', '--merged=' . $tag, $preReleaseTagPattern]);

        return array_map([$this, 'getVersionFromTag'], $tags);
    }

    private function getVersionFromTag(string $tag): string
    {
        return substr($tag, strlen($this->tagPrefix));
    }

    /**
     * @return Commit[]
     */
    private function getCommitsInRange(string $revisionRange, ?string $pattern = null): array
    {
        $arguments = [$revisionRange];

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
                list($title, $body) = explode("\x1F", trim($commit));

                return new Commit($title, $body);
            },
            $commits
        );

        return $commits;
    }

    /**
     * Returns a revision range that describes the changes introduced in a
     * version.
     *
     * For example, if there is a tag v1.0.1 and a tag v1.0.0, this will return
     * v1.0.0..v1.0.1.
     */
    private function getRevisionRangeForVersion(string $version): string
    {
        $tag = $this->getTagForVersion($version);

        try {
            return sprintf('%s..%s', $this->describe($tag . '^'), $tag);
        } catch (ReleaseNotFoundException $exception) {
            return $tag;
        }
    }

    private function describe(string $object = 'HEAD'): string
    {
        try {
            return self::execute(
                'describe',
                [
                    '--abbrev=0',
                    '--tags',
                    sprintf('--match "%s%s"', $this->tagPrefix, self::VERSION_GLOB),
                    $object,
                ]
            )[0];
        } catch (GitException $exception) {
            throw new ReleaseNotFoundException('No release could be found', 0, $exception);
        }
    }
}
