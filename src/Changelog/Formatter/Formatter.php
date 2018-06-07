<?php
declare(strict_types=1);

namespace Leviy\ReleaseTool\Changelog\Formatter;

interface Formatter
{
    /**
     * @param string[] $changes
     *
     * @return string[]
     */
    public function formatChanges(array $changes): array;
}
