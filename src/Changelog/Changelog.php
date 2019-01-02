<?php
declare(strict_types=1);

namespace Leviy\ReleaseTool\Changelog;

final class Changelog
{
    /**
     * @var string[]
     */
    private $changes;

    /**
     * @param string[] $changes
     */
    public function __construct(array $changes)
    {
        $this->changes = $changes;
    }

    /**
     * @return string[]
     */
    public function getChanges(): array
    {
        return $this->changes;
    }
}
