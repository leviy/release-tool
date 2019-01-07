<?php
declare(strict_types=1);

namespace Leviy\ReleaseTool\Tests\Unit\Changelog\Formatter\Filter;

use Leviy\ReleaseTool\Changelog\Formatter\Filter\IssueLinkFilter;
use PHPUnit\Framework\TestCase;

class IssueLinkFilterTest extends TestCase
{
    public function testThatAnIssueReferenceIsTransformedToALink(): void
    {
        $filter = new IssueLinkFilter('/(RT-[0-9]+)/', 'https://issuetracker.com/issues/$1');

        $output = $filter->filter('Fixed RT-123 by doing this thing');

        $this->assertSame('Fixed [RT-123](https://issuetracker.com/issues/RT-123) by doing this thing', $output);
    }
}
