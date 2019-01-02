<?php
declare(strict_types=1);

namespace Leviy\ReleaseTool\Tests\Unit\Changelog\Formatter;

use Leviy\ReleaseTool\Changelog\Changelog;
use Leviy\ReleaseTool\Changelog\Formatter\Filter\Filter;
use Leviy\ReleaseTool\Changelog\Formatter\MarkdownFormatter;
use Mockery;
use PHPUnit\Framework\TestCase;

class MarkdownFormatterTest extends TestCase
{
    public function testThatChangesAreFormattedAsAList(): void
    {
        $formatter = new MarkdownFormatter([]);

        $changelog = new Changelog(
            [
                'Title of first change',
                'Title of second change',
            ]
        );

        $output = $formatter->format($changelog);

        $this->assertContains('* Title of first change', $output);
        $this->assertContains('* Title of second change', $output);
    }

    public function testThatTheOutputContainsAHeader(): void
    {
        $formatter = new MarkdownFormatter([]);

        $changelog = new Changelog(
            [
                'Some change',
            ]
        );

        $output = $formatter->format($changelog);

        $this->assertContains('# Changelog', $output);
    }

    public function testThatFiltersAreApplied(): void
    {
        $filter = Mockery::mock(Filter::class);
        $filter->shouldReceive('filter')->andReturn('Filtered change line');

        $formatter = new MarkdownFormatter([$filter]);

        $changelog = new Changelog(
            [
                'Some change',
            ]
        );

        $this->assertContains('Filtered change line', $formatter->format($changelog));
        $this->assertNotContains('Some change', $formatter->format($changelog));
    }
}
