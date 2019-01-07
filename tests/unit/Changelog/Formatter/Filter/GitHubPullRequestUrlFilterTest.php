<?php
declare(strict_types=1);

namespace Leviy\ReleaseTool\Tests\Unit\Changelog\Formatter\Filter;

use Leviy\ReleaseTool\Changelog\Formatter\Filter\GitHubPullRequestUrlFilter;
use PHPUnit\Framework\TestCase;

class GitHubPullRequestUrlFilterTest extends TestCase
{
    public function testThatAPullRequestReferenceIsTransformedToALink(): void
    {
        $filter = new GitHubPullRequestUrlFilter('org/repo');

        $output = $filter->filter('My great PR title (pull request #23)');

        $this->assertSame('My great PR title (pull request [#23](https://github.com/org/repo/pull/23))', $output);
    }
}
