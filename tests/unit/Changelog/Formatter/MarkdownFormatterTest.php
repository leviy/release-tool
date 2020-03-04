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

        $this->assertStringContainsString('# Changelog for 1.0.0', $output);
        $this->assertStringContainsString('# Changelog for 1.1.0', $output);
    }

    public function testThatTheOutputContainsAListItemForEveryChange(): void
    {
        $formatter = new MarkdownFormatter([]);

        $changelog = new Changelog();
        $changelog->addVersion('1.0.0', ['First change', 'Second change']);

        $output = $formatter->format($changelog);

        $this->assertStringContainsString('* First change', $output);
        $this->assertStringContainsString('* Second change', $output);
    }

    public function testThatVersionsAreShownInReversedOrder(): void
    {
        $formatter = new MarkdownFormatter([]);

        $changelog = new Changelog();
        $changelog->addVersion('1.0.0-alpha.1', ['Alpha change']);
        $changelog->addVersion('1.0.0-beta.1', ['Beta change']);
        $changelog->addVersion('1.0.0', ['Release change']);

        $output = $formatter->format($changelog);

        $expected = <<<EXPECTED
# Changelog for 1.0.0

* Release change

# Changelog for 1.0.0-beta.1

* Beta change

# Changelog for 1.0.0-alpha.1

* Alpha change
EXPECTED;

        $this->assertStringContainsString($expected, $output);
    }

    public function testThatFiltersAreApplied(): void
    {
        $filter = Mockery::mock(Filter::class);
        $filter->shouldReceive('filter')->andReturn('Filtered change line');

        $formatter = new MarkdownFormatter([$filter]);

        $changelog = new Changelog();
        $changelog->addVersion('1.0.0', ['Some change']);

        $this->assertStringContainsString('Filtered change line', $formatter->format($changelog));
        $this->assertStringNotContainsString('Some change', $formatter->format($changelog));
    }
}
