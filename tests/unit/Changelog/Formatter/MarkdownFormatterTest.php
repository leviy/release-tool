<?php
declare(strict_types=1);

namespace Leviy\ReleaseTool\Tests\Unit\Changelog\Formatter;

use Leviy\ReleaseTool\Changelog\Formatter\MarkdownFormatter;
use PHPUnit\Framework\TestCase;

class MarkdownFormatterTest extends TestCase
{
    public function testThatChangesAreFormattedAsAList(): void
    {
        $formatter = new MarkdownFormatter('');

        $changes = $formatter->formatChanges(
            [
                'Title of first change',
                'Title of second change',
            ]
        );

        $this->assertContains('* Title of first change', $changes);
        $this->assertContains('* Title of second change', $changes);
    }

    public function testThatPullRequestNumbersLinkToGithub(): void
    {
        $formatter = new MarkdownFormatter('org/repo');

        $changes = $formatter->formatChanges(
            [
                'Some change (pull request #3)',
                'Other change (pull request #457)',
            ]
        );

        $this->assertContains('* Some change (pull request [#3](https://github.com/org/repo/pull/3))', $changes);
        $this->assertContains('* Other change (pull request [#457](https://github.com/org/repo/pull/457))', $changes);
    }

    public function testThatIssueNumbersLinkToIssueTracker(): void
    {
        $formatter = new MarkdownFormatter('org/repo', '/(RT-[0-9]+)/', 'https://issuetracker.com/$1');

        $changes = $formatter->formatChanges(
            [
                'RT-123: Some change',
                'Other change',
            ]
        );

        $this->assertContains('* [RT-123](https://issuetracker.com/RT-123): Some change', $changes);
        $this->assertContains('* Other change', $changes);
    }

    public function testThatTheOutputContainsAHeader(): void
    {
        $formatter = new MarkdownFormatter('');

        $changes = $formatter->formatChanges(['Some change']);

        $this->assertContains('# Changelog', $changes);
    }
}
