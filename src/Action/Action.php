<?php
declare(strict_types=1);

namespace Leviy\ReleaseTool\Action;

interface Action
{
    public function release(string $version): void;
}
