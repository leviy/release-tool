<?php
declare(strict_types=1);

namespace Leviy\ReleaseTool\Console;

use function strlen;
use function strpos;
use function substr;

final class VersionHelper
{
    private const VERSION_PREFIX = 'v';

    public static function removeVersionPrefix(string $version): string
    {
        if (strpos($version, self::VERSION_PREFIX) === 0) {
            return substr($version, strlen(self::VERSION_PREFIX));
        }

        return $version;
    }
}
