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

    /**
     * @var string
     */
    private $tagPrefix;

    public function __construct(string $tagPrefix = '')
    {
        $this->tagPrefix = $tagPrefix;
    }

    public function getLastVersion(): string
    {
        try {
            $tag = $this->executeGitCommand(
                'describe',
                [
                    '--tags',
                    '--abbrev=0',
                    sprintf('--match "%s%s"', $this->tagPrefix, self::VERSION_GLOB),
                ]
            )[0];
        } catch (GitException $exception) {
            if ($exception->getMessage() === 'fatal: No names found, cannot describe anything.') {
                throw new ReleaseNotFoundException('No release could be found', 0, $exception);
            }

            throw $exception;
        }

        return $this->getVersionFromTag($tag);
    }

    /**
     * @param string   $command
     * @param string[] $arguments
     *
     * @return string[]
     */
    private function executeGitCommand(string $command, array $arguments = []): array
    {
        $command = sprintf('git %s %s', $command, implode(' ', $arguments));

        exec($command . ' 2>&1', $output, $exitCode);

        if ($exitCode > 0) {
            throw new GitException(implode(PHP_EOL, $output));
        }

        return $output;
    }

    private function getVersionFromTag(string $tag): string
    {
        return substr($tag, strlen($this->tagPrefix));
    }
}
