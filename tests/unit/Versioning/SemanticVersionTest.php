<?php
declare(strict_types=1);

namespace Leviy\ReleaseTool\Tests\Unit\Versioning;

use InvalidArgumentException;
use Leviy\ReleaseTool\Versioning\SemanticVersion;
use PHPUnit\Framework\TestCase;

class SemanticVersionTest extends TestCase
{
    public function testThatItCanBeCreatedFromAString(): void
    {
        $version = SemanticVersion::createFromVersionString('1.2.3');

        $this->assertSame(1, $version->getMajorVersion());
        $this->assertSame(2, $version->getMinorVersion());
        $this->assertSame(3, $version->getPatchVersion());

        $this->assertSame('1.2.3', $version->getVersion());
    }

    /**
     * @dataProvider getInvalidVersions
     */
    public function testThatAnInvalidVersionThrowsAnException(string $invalidVersion): void
    {
        $this->expectException(InvalidArgumentException::class);

        SemanticVersion::createFromVersionString($invalidVersion);
    }

    /**
     * @return string[][]
     */
    public function getInvalidVersions(): array
    {
        return [
            ['1'],
            ['1.1'],
            ['foo'],
        ];
    }

    public function testThatThePatchVersionIsIncremented(): void
    {
        $version = SemanticVersion::createFromVersionString('1.2.3');

        $newVersion = $version->incrementPatchVersion();

        $this->assertSame('1.2.4', $newVersion->getVersion());
    }

    public function testThatTheVersionInstanceIsNotChanged(): void
    {
        $version = SemanticVersion::createFromVersionString('1.2.3');

        $version->incrementPatchVersion();

        $this->assertSame('1.2.3', $version->getVersion());
    }

    public function testThatTheMinorVersionIsIncremented(): void
    {
        $version = SemanticVersion::createFromVersionString('1.2.3');

        $newVersion = $version->incrementMinorVersion();

        $this->assertSame('1.3.0', $newVersion->getVersion());
    }

    public function testThatTheMajorVersionIsIncremented(): void
    {
        $version = SemanticVersion::createFromVersionString('1.2.3');

        $newVersion = $version->incrementMajorVersion();

        $this->assertSame('2.0.0', $newVersion->getVersion());
    }
}
