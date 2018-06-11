<?php
declare(strict_types=1);

namespace Leviy\ReleaseTool\Interaction;

interface InformationCollector
{
    public function askConfirmation(string $question): bool;

    /**
     * @param string   $question
     * @param string[] $choices
     *
     * @return string
     */
    public function askMultipleChoice(string $question, array $choices): string;
}
