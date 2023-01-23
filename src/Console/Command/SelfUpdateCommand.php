<?php
declare(strict_types=1);

namespace Leviy\ReleaseTool\Console\Command;

use Leviy\ReleaseTool\Console\Application;
use SelfUpdate\SelfUpdateCommand as BaseSelfUpdateCommand;
use function preg_match;
use function sprintf;

final class SelfUpdateCommand extends BaseSelfUpdateCommand
{
    private const REPOSITORY = 'leviy/release-tool';

    private const NORMALIZED_VERSION_REGEX = '/^(?<major>\d{1,5}).(?<minor>\d+).(?<patch>\d+).0$/';

    public function __construct()
    {
        parent::__construct(Application::NAME, Application::VERSION, self::REPOSITORY);
    }

    /**
     * The method parent::getLatestReleaseFromGithub returns an array with a normalized version number.
     * The normalized version is suffixed with an additional ".0" which doesn't really fit the commonly
     * used "major.minor.patch" format. Here we detect whether an additional ".0" is suffixed to the
     * version number and change it to the "major.minor.patch" format.
     *
     * @param array<string, string | bool> $options
     * @return array<string, string>
     */
    public function getLatestReleaseFromGithub(array $options): ?array
    {
        $latestReleaseFromGithub = parent::getLatestReleaseFromGithub($options);

        if ($latestReleaseFromGithub === null) {
            return null;
        }

        if (!preg_match(self::NORMALIZED_VERSION_REGEX, $latestReleaseFromGithub['version'], $matches)) {
            return $latestReleaseFromGithub;
        }

        $latestReleaseFromGithub['version'] = sprintf(
            '%s.%s.%s',
            $matches['major'],
            $matches['minor'],
            $matches['patch'],
        );

        return $latestReleaseFromGithub;
    }
}
