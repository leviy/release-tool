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
    public function testThatTheOutputContainsAHeaderForEveryVersion(): void
    {
        $formatter = new MarkdownFormatter([]);

        $changelog = new Changelog();
        $changelog->addVersion('1.0.0', []);
        $changelog->addVersion('1.1.0', []);

        $output = $formatter->format($changelog);

        $this->assertContains('# Changelog for 1.0.0', $output);
        $this->assertContains('# Changelog for 1.1.0', $output);
    }

    public function testThatTheOutputContainsAListItemForEveryChange(): void
    {
        $formatter = new MarkdownFormatter([]);

        $changelog = new Changelog();
        $changelog->addVersion('1.0.0', ['First change', 'Second change']);

        $output = $formatter->format($changelog);

        $this->assertContains('* First change', $output);
        $this->assertContains('* Second change', $output);
    }

    public function testThatVersionsAreShownInReversedOrder(): void
    {
        $formatter = new MarkdownFormatter([]);

        $changelog = new Changelog();
        $changelog->addVersion('1.0.0', ['Some change']);
        $changelog->addVersion('1.1.0', ['Other change']);

        $output = $formatter->format($changelog);

        $expected = <<<EXPECTED
# Changelog for 1.1.0

* Other change

# Changelog for 1.0.0

* Some change
EXPECTED;

        $this->assertContains($expected, $output);
    }

    public function testThatFiltersAreApplied(): void
    {
        $filter = Mockery::mock(Filter::class);
        $filter->shouldReceive('filter')->andReturn('Filtered change line');

        $formatter = new MarkdownFormatter([$filter]);

        $changelog = new Changelog();
        $changelog->addVersion('1.0.0', ['Some change']);

        $this->assertContains('Filtered change line', $formatter->format($changelog));
        $this->assertNotContains('Some change', $formatter->format($changelog));
    }
}
