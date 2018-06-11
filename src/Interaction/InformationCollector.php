<?php
declare(strict_types=1);

namespace Leviy\ReleaseTool\Interaction;

interface InformationCollector
{
    public function askConfirmation(string $question): bool;

    /**
     * @param string      $question
     * @param string[]    $choices
     * @param string|null $default
     *
     * @return string
     */
    public function askMultipleChoice(string $question, array $choices, ?string $default = null): string;
}
