<?php
declare(strict_types=1);

namespace Leviy\ReleaseTool\Changelog\Formatter\Filter;

interface Filter
{
    public function filter(string $line): string;
}
