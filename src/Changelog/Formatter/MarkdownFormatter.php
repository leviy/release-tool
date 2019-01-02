<?php
declare(strict_types=1);

namespace Leviy\ReleaseTool\Changelog\Formatter;

use Assert\Assertion;
use Leviy\ReleaseTool\Changelog\Changelog;
use Leviy\ReleaseTool\Changelog\Formatter\Filter\Filter;
use function array_map;
use function array_merge;
use function implode;
use const PHP_EOL;

final class MarkdownFormatter implements Formatter
{
    /**
     * @var Filter[]
     */
    private $filters;

    /**
     * @param Filter[] $filters
     */
    public function __construct(array $filters)
    {
        Assertion::allIsInstanceOf($filters, Filter::class);

        $this->filters = $filters;
    }

    public function format(Changelog $changelog): string
    {
        $changes = array_map(
            function (string $line): string {
                $line = $this->applyFilters($line);

                return '* ' . $line;
            },
            $changelog->getChanges()
        );

        $lines = array_merge(
            [
                '# Changelog',
                '',
            ],
            $changes
        );

        return implode(PHP_EOL, $lines);
    }

    private function applyFilters(string $line): string
    {
        foreach ($this->filters as $filter) {
            $line = $filter->filter($line);
        }

        return $line;
    }
}
