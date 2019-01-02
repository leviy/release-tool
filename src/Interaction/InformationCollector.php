<?php
declare(strict_types=1);

namespace Leviy\ReleaseTool\Interaction;

interface InformationCollector
{
    public function askConfirmation(string $question): bool;

    /**
     * @param string[] $choices
     */
    public function askMultipleChoice(string $question, array $choices, ?string $default = null): string;
}
