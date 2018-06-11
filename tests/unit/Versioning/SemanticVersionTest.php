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

    public function testThatItHandlesMultiDigitVersions(): void
    {
        $version = SemanticVersion::createFromVersionString('11.20.31');

        $this->assertSame(11, $version->getMajorVersion());
        $this->assertSame(20, $version->getMinorVersion());
        $this->assertSame(31, $version->getPatchVersion());

        $this->assertSame('11.20.31', $version->getVersion());
    }

    /**
     * @dataProvider getPreReleaseVersions
     */
    public function testThatAPreReleaseVersionCanBeCreated(string $versionString, string $preReleaseString): void
    {
        $version = SemanticVersion::createFromVersionString($versionString);

        $this->assertSame($preReleaseString, $version->getPreReleaseIdentifier());
        $this->assertSame($versionString, $version->getVersion());
    }

    /**
     * @return string[][]
     */
    public function getPreReleaseVersions(): array
    {
        return [
            ['1.0.0-alpha', 'alpha'],
            ['1.0.0-alpha.1', 'alpha.1'],
            ['1.0.0-0.3.7', '0.3.7'],
            ['1.0.0-x.7.z.92', 'x.7.z.92'],
        ];
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
        $version = SemanticVersion::createFromVersionString('1.2.13');

        $newVersion = $version->incrementPatchVersion();

        $this->assertSame('1.2.14', $newVersion->getVersion());
    }

    public function testThatTheVersionInstanceIsNotChanged(): void
    {
        $version = SemanticVersion::createFromVersionString('1.2.13');

        $version->incrementPatchVersion();

        $this->assertSame('1.2.13', $version->getVersion());
    }

    public function testThatTheMinorVersionIsIncremented(): void
    {
        $version = SemanticVersion::createFromVersionString('1.2.13');

        $newVersion = $version->incrementMinorVersion();

        $this->assertSame('1.3.0', $newVersion->getVersion());
    }

    public function testThatTheMajorVersionIsIncremented(): void
    {
        $version = SemanticVersion::createFromVersionString('1.2.13');

        $newVersion = $version->incrementMajorVersion();

        $this->assertSame('2.0.0', $newVersion->getVersion());
    }
}
