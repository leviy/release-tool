<?php
declare(strict_types=1);

namespace Leviy\ReleaseTool\Vcs;

use function exec;
use function implode;
use function sprintf;
use function strlen;
use function substr;
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
     * @param string   $command
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

    private function getVersionFromTag(string $tag): string
    {
        return substr($tag, strlen($this->tagPrefix));
    }
}
