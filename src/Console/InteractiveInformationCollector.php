<?php
declare(strict_types=1);

namespace Leviy\ReleaseTool\Console;

use Leviy\ReleaseTool\Interaction\InformationCollector;
use Symfony\Component\Console\Style\StyleInterface;

final class InteractiveInformationCollector implements InformationCollector
{
    /**
     * @var StyleInterface
     */
    private $style;

    public function __construct(StyleInterface $style)
    {
        $this->style = $style;
    }

    public function askConfirmation(string $question): bool
    {
        return $this->style->confirm($question);
    }

    /**
     * @inheritdoc
     */
    public function askMultipleChoice(string $question, array $choices): string
    {
        return $this->style->choice($question, $choices);
    }
}
