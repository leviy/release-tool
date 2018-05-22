<?php
declare(strict_types=1);

namespace Leviy\ReleaseTool\Tests\Unit\Console\Command;

use Leviy\ReleaseTool\Console\Command\ReleaseCommand;
use Leviy\ReleaseTool\Vcs\VersionControlSystem;
use Leviy\ReleaseTool\Versioning\Strategy;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class ReleaseCommandTest extends TestCase
{
    use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

    /**
     * @var MockInterface|VersionControlSystem
     */
    private $vcs;

    /**
     * @var MockInterface|Strategy
     */
    private $versioningStrategy;

    protected function setUp(): void
    {
        $this->vcs = Mockery::spy(VersionControlSystem::class);
        $this->versioningStrategy = Mockery::mock(Strategy::class);
    }

    public function testThatItCreatesANewRelease(): void
    {
        $command = new ReleaseCommand($this->vcs, $this->versioningStrategy);

        $application = new Application();
        $application->add($command);

        $commandTester = new CommandTester($command);

        $commandTester->setInputs(['yes']);
        $commandTester->execute(['version' => '1.2.0']);

        $this->vcs->shouldHaveReceived('createVersion', ['1.2.0']);
    }

    public function testThatItUsesAVersioningStrategyToDetermineTheNextVersion(): void
    {
        $command = new ReleaseCommand($this->vcs, $this->versioningStrategy);

        $application = new Application();
        $application->add($command);

        $this->versioningStrategy->shouldReceive('getNextVersion')->andReturn('2.3.0');

        $commandTester = new CommandTester($command);
        $commandTester->setInputs(['yes']);

        $commandTester->execute([]);

        $this->vcs->shouldHaveReceived('createVersion', ['2.3.0']);
    }
}
