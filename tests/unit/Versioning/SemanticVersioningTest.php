<?php
declare(strict_types=1);

namespace Leviy\ReleaseTool\Tests\Unit\Versioning;

use Leviy\ReleaseTool\Interaction\InformationCollector;
use Leviy\ReleaseTool\Versioning\SemanticVersion;
use Leviy\ReleaseTool\Versioning\SemanticVersioning;
use Mockery;
use PHPUnit\Framework\TestCase;

class SemanticVersioningTest extends TestCase
{
    /**
     * @var Mockery\MockInterface|InformationCollector
     */
    private $informationCollector;

    protected function setUp(): void
    {
        $this->informationCollector = Mockery::mock(InformationCollector::class);
    }

    public function testThatBackwardIncompatibilitiesResultInMajorVersionBump(): void
    {
        $semver = new SemanticVersioning();

        $this->informationCollector
            ->shouldReceive('askConfirmation')
            ->with('Does this release contain backward incompatible changes?')
            ->andReturn(true);

        $version = $semver->getNextVersion(
            SemanticVersion::createFromVersionString('1.0.0'),
            $this->informationCollector
        );

        $this->assertEquals(SemanticVersion::createFromVersionString('2.0.0'), $version);
    }

    public function testThatNewFeaturesResultInMinorVersionBump(): void
    {
        $semver = new SemanticVersioning();

        $this->informationCollector
            ->shouldReceive('askConfirmation')
            ->with('Does this release contain backward incompatible changes?')
            ->andReturn(false);

        $this->informationCollector
            ->shouldReceive('askConfirmation')
            ->with('Does this release contain new features?')
            ->andReturn(true);

        $version = $semver->getNextVersion(
            SemanticVersion::createFromVersionString('1.0.0'),
            $this->informationCollector
        );

        $this->assertEquals(SemanticVersion::createFromVersionString('1.1.0'), $version);
    }

    public function testThatBugfixesResultInPatchVersionBump(): void
    {
        $semver = new SemanticVersioning();

        $this->informationCollector
            ->shouldReceive('askConfirmation')
            ->with('Does this release contain backward incompatible changes?')
            ->andReturn(false);

        $this->informationCollector
            ->shouldReceive('askConfirmation')
            ->with('Does this release contain new features?')
            ->andReturn(false);

        $version = $semver->getNextVersion(
            SemanticVersion::createFromVersionString('1.0.0'),
            $this->informationCollector
        );

        $this->assertEquals(SemanticVersion::createFromVersionString('1.0.1'), $version);
    }

    /**
     * @dataProvider getPreReleaseTypes
     */
    public function testThatThePreReleaseTypeCanBeChosen(string $choice, string $type): void
    {
        $semver = new SemanticVersioning();

        $this->informationCollector
            ->shouldReceive('askConfirmation')
            ->andReturn(false, true);

        $this->informationCollector
            ->shouldReceive('askMultipleChoice')
            ->andReturn($choice);

        $version = $semver->getNextPreReleaseVersion(
            SemanticVersion::createFromVersionString('1.0.0'),
            $this->informationCollector
        );

        $this->assertEquals(SemanticVersion::createFromVersionString('1.1.0-' . $type . '.1'), $version);
    }

    /**
     * @return string[][]
     */
    public function getPreReleaseTypes(): array
    {
        return [
            ['a', 'alpha'],
            ['b', 'beta'],
            ['rc', 'rc'],
        ];
    }

    public function testThatTheFirstPreReleaseIncreasesTheVersionNumber(): void
    {
        $semver = new SemanticVersioning();

        $this->informationCollector
            ->shouldReceive('askConfirmation')
            ->andReturn(false, true);

        $this->informationCollector
            ->shouldReceive('askMultipleChoice')
            ->andReturn('a');

        $version = $semver->getNextPreReleaseVersion(
            SemanticVersion::createFromVersionString('1.0.0'),
            $this->informationCollector
        );

        $this->assertEquals(SemanticVersion::createFromVersionString('1.1.0-alpha.1'), $version);
    }

    public function testThatAdditionalPreReleasesDoNotIncreaseTheVersionNumber(): void
    {
        $semver = new SemanticVersioning();

        $this->informationCollector
            ->shouldReceive('askConfirmation')
            ->andReturn(false, true);

        $this->informationCollector
            ->shouldReceive('askMultipleChoice')
            ->andReturn('a');

        $version = $semver->getNextPreReleaseVersion(
            SemanticVersion::createFromVersionString('1.1.0-alpha.1'),
            $this->informationCollector
        );

        $this->assertEquals(SemanticVersion::createFromVersionString('1.1.0-alpha.2'), $version);
    }
}
