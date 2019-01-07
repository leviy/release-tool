<?php
declare(strict_types=1);

namespace Leviy\ReleaseTool\Changelog\Formatter;

use Assert\Assertion;
use Leviy\ReleaseTool\Changelog\Changelog;
use Leviy\ReleaseTool\Changelog\Formatter\Filter\Filter;
use function array_reverse;
use function implode;
use function sprintf;
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
        $lines = [];

        $versions = $changelog->getVersions();
        $versions = array_reverse($versions);

        foreach ($versions as $version) {
            $lines[] = sprintf('# Changelog for %s', $version);
            $lines[] = '';

            foreach ($changelog->getChangesForVersion($version) as $change) {
                $lines[] = '* ' . $this->applyFilters($change);
            }

            $lines[] = '';
        }

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
