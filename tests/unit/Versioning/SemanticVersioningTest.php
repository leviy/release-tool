<?php
declare(strict_types=1);

namespace Leviy\ReleaseTool\Tests\Unit\Versioning;

use Leviy\ReleaseTool\Interaction\InformationCollector;
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

        $version = $semver->getNextVersion('1.0.0', $this->informationCollector);

        $this->assertSame('2.0.0', $version);
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

        $version = $semver->getNextVersion('1.0.0', $this->informationCollector);

        $this->assertSame('1.1.0', $version);
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

        $version = $semver->getNextVersion('1.0.0', $this->informationCollector);

        $this->assertSame('1.0.1', $version);
    }
}
