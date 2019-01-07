<?php
declare(strict_types=1);

namespace Leviy\ReleaseTool\Tests\Unit\Changelog;

use Leviy\ReleaseTool\Changelog\Changelog;
use PHPUnit\Framework\TestCase;

class ChangelogTest extends TestCase
{
    public function testThatItReturnsVersionNumbers(): void
    {
        $changelog = new Changelog();

        $changelog->addVersion('1.0.0', []);

        $this->assertSame(['1.0.0'], $changelog->getVersions());
    }

    public function testVersionNumbersAreReturnedInTheOrderTheyWereAdded(): void
    {
        $changelog = new Changelog();

        $changelog->addVersion('1.0.0-alpha.1', []);
        $changelog->addVersion('1.0.0-beta.1', []);
        $changelog->addVersion('1.0.0-beta.2', []);
        $changelog->addVersion('1.0.0', []);

        $this->assertSame(['1.0.0-alpha.1', '1.0.0-beta.1', '1.0.0-beta.2', '1.0.0'], $changelog->getVersions());
    }
}
