<?php
declare(strict_types=1);

namespace Leviy\ReleaseTool\Tests\Unit\Changelog\Formatter;

use Leviy\ReleaseTool\Changelog\Changelog;
use Leviy\ReleaseTool\Changelog\Formatter\MarkdownFormatter;
use PHPUnit\Framework\TestCase;

class MarkdownFormatterTest extends TestCase
{
    public function testThatChangesAreFormattedAsAList(): void
    {
        $formatter = new MarkdownFormatter('');

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

    public function testThatPullRequestNumbersLinkToGithub(): void
    {
        $formatter = new MarkdownFormatter('org/repo');

        $changelog = new Changelog(
            [
                'Some change (pull request #3)',
                'Other change (pull request #457)',
            ]
        );

        $output = $formatter->format($changelog);

        $this->assertContains('* Some change (pull request [#3](https://github.com/org/repo/pull/3))', $output);
        $this->assertContains('* Other change (pull request [#457](https://github.com/org/repo/pull/457))', $output);
    }

    public function testThatIssueNumbersLinkToIssueTracker(): void
    {
        $formatter = new MarkdownFormatter('org/repo', '/(RT-[0-9]+)/', 'https://issuetracker.com/$1');

        $changelog = new Changelog(
            [
                'RT-123: Some change',
                'Other change',
            ]
        );

        $output = $formatter->format($changelog);

        $this->assertContains('* [RT-123](https://issuetracker.com/RT-123): Some change', $output);
        $this->assertContains('* Other change', $output);
    }

    public function testThatTheOutputContainsAHeader(): void
    {
        $formatter = new MarkdownFormatter('');

        $changelog = new Changelog(
            [
                'Some change',
            ]
        );

        $output = $formatter->format($changelog);

        $this->assertContains('# Changelog', $output);
    }
}
