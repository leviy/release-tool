<?php
declare(strict_types=1);

namespace Leviy\ReleaseTool\Changelog\Filter;

use Leviy\ReleaseTool\Changelog\Changelog;

interface Filter
{
    /**
     * Returns a filtered instance of the given changelog. Useful to limit a
     * changelog to the last version or another criterium.
     */
    public function filter(Changelog $changelog): Changelog;
}
