<?php
declare(strict_types=1);

namespace Leviy\ReleaseTool\Changelog\Formatter;

use Leviy\ReleaseTool\Changelog\Changelog;

interface Formatter
{
    public function format(Changelog $changelog): string;
}
