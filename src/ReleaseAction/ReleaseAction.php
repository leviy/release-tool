<?php
declare(strict_types=1);

namespace Leviy\ReleaseTool\ReleaseAction;

interface ReleaseAction
{
    public function execute(string $version): void;
}
