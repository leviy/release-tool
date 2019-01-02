<?php
declare(strict_types=1);

namespace Leviy\ReleaseTool\Changelog\Filter;

use Leviy\ReleaseTool\Changelog\Changelog;
use function end;

/**
 * Returns a changelog that contains only the changes introduced by the last
 * version
 */
final class LastReleaseFilter implements Filter
{
    public function filter(Changelog $changelog): Changelog
    {
        $versions = $changelog->getVersions();
        $lastVersion = end($versions);

        $return = new Changelog();
        $return->addVersion($lastVersion, $changelog->getChangesForVersion($lastVersion));

        return $return;
    }
}
