<?php
declare(strict_types=1);

namespace Leviy\ReleaseTool\ReleaseAction;

interface ReleaseAction
{
    /**
     * @param string   $version
     * @param string[] $changeset
     *
     * @return void
     */
    public function execute(string $version, array $changeset): void;
}
