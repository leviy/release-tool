<?php
declare(strict_types=1);

namespace Leviy\ReleaseTool\Tests\Unit;

use Assert\InvalidArgumentException;
use Leviy\ReleaseTool\Interaction\InformationCollector;
use Leviy\ReleaseTool\ReleaseAction\ReleaseAction;
use Leviy\ReleaseTool\ReleaseManager;
use Leviy\ReleaseTool\Vcs\VersionControlSystem;
use Leviy\ReleaseTool\Versioning\Strategy;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;

class ReleaseManagerTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @var MockInterface|VersionControlSystem
     */
    private $vcs;

    /**
     * @var MockInterface|Strategy
     */
    private $versioningStrategy;

    /**
     * @var MockInterface|InformationCollector
     */
    private $informationCollector;

    /**
     * @var MockInterface|ReleaseAction
     */
    private $releaseAction;

    public function setUp(): void
    {
        $this->vcs = Mockery::spy(VersionControlSystem::class);
        $this->versioningStrategy = Mockery::mock(Strategy::class);
        $this->releaseAction = Mockery::spy(ReleaseAction::class);
        $this->informationCollector = Mockery::mock(InformationCollector::class);
    }

    public function testThatInstantiationThrowsAnErrorWhenActionIsNotReleaseAction(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $releaseManager = new ReleaseManager(
            $this->vcs,
            $this->versioningStrategy,
            ['test-action']
        );
    }

    public function testThatVersionIsCreatedAndPushed(): void
    {
        $releaseManager = new ReleaseManager(
            $this->vcs,
            $this->versioningStrategy,
            []
        );

        $this->informationCollector->shouldReceive('askConfirmation')->andReturnTrue();

        $releaseManager->release('9.1.1', $this->informationCollector);

        $this->vcs->shouldHaveReceived('createVersion', ['9.1.1']);
        $this->vcs->shouldHaveReceived('pushVersion', ['9.1.1']);
    }

    public function testThatVersionIsCreatedButNotPushed(): void
    {
        $releaseManager = new ReleaseManager(
            $this->vcs,
            $this->versioningStrategy,
            []
        );

        $this->informationCollector->shouldReceive('askConfirmation')->andReturnFalse();

        $releaseManager->release('9.1.1', $this->informationCollector);

        $this->vcs->shouldHaveReceived('createVersion', ['9.1.1']);
        $this->vcs->shouldNotHaveReceived('pushVersion', ['9.1.1']);
    }

    public function testThatAdditionalActionsAreExecutedDuringRelease(): void
    {
        $releaseManager = new ReleaseManager(
            $this->vcs,
            $this->versioningStrategy,
            [$this->releaseAction]
        );

        $this->informationCollector->shouldReceive('askConfirmation')->andReturnFalse();

        $releaseManager->release('9.1.1', $this->informationCollector);

        $this->releaseAction->shouldHaveReceived('execute')->once();
    }

    public function testThatMultipleAdditionalActionsAreExecutedInGivenOrder(): void
    {
        $releaseAction = Mockery::mock(ReleaseAction::class);
        $additionalAction = Mockery::mock(ReleaseAction::class);

        $releaseManager = new ReleaseManager(
            $this->vcs,
            $this->versioningStrategy,
            [$releaseAction, $additionalAction]
        );

        $this->informationCollector->shouldReceive('askConfirmation')->andReturnFalse();

        $releaseAction->shouldReceive('execute')->once()->globally()->ordered();
        $additionalAction->shouldReceive('execute')->once()->globally()->ordered();

        $releaseManager->release('9.1.1', $this->informationCollector);
    }
}
