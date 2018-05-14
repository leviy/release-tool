<?php
declare(strict_types=1);

namespace Leviy\ReleaseTool\Tests\Unit;

use Leviy\ReleaseTool\Action\Action;
use Leviy\ReleaseTool\ReleaseManager;
use Mockery;
use PHPUnit\Framework\TestCase;

class ReleaseManagerTest extends TestCase
{
    use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

    public function testThatItPerformsTheReleaseActions(): void
    {
        $action1 = Mockery::spy(Action::class);

        $manager = new ReleaseManager([$action1]);

        $manager->release('1.0.0');

        $action1->shouldHaveReceived('release', ['1.0.0']);
    }

    public function testThatTheReleaseActionsArePerformedInOrder(): void
    {
        $action1 = Mockery::mock(Action::class);
        $action2 = Mockery::mock(Action::class);

        $action1->shouldReceive('release')->with('1.0.0')->globally()->ordered();
        $action2->shouldReceive('release')->with('1.0.0')->globally()->ordered();

        $manager = new ReleaseManager([$action1, $action2]);

        $manager->release('1.0.0');
    }
}
