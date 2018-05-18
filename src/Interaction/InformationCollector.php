<?php
declare(strict_types=1);

namespace Leviy\ReleaseTool\Interaction;

interface InformationCollector
{
    public function askConfirmation(string $question): bool;
}
