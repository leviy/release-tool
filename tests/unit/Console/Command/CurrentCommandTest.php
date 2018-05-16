<?php
declare(strict_types=1);

namespace Leviy\ReleaseTool\Tests\Unit\Console\Command;

use Leviy\ReleaseTool\Console\Command\CurrentCommand;
use Leviy\ReleaseTool\Vcs\ReleaseNotFoundException;
use Leviy\ReleaseTool\Vcs\VersionControlSystem;
use Mockery;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class CurrentCommandTest extends TestCase
{
    /**
     * @var Mockery\MockInterface|VersionControlSystem
     */
    private $vcs;

    protected function setUp(): void
    {
        $this->vcs = Mockery::mock(VersionControlSystem::class);
    }

    public function testThatItOutputsTheVersionNumber(): void
    {
        $command = new CurrentCommand($this->vcs);

        $application = new Application();
        $application->add($command);

        $commandTester = new CommandTester($command);

        $this->vcs->shouldReceive('getLastVersion')->andReturn('3.2.0');

        $commandTester->execute([]);

        $this->assertContains('Current version: 3.2.0', $commandTester->getDisplay());
        $this->assertSame(0, $commandTester->getStatusCode());
    }

    public function testThatItShowsAnErrorMessageIfNoVersionIsFound(): void
    {
        $command = new CurrentCommand($this->vcs);

        $application = new Application();
        $application->add($command);

        $commandTester = new CommandTester($command);

        $this->vcs->shouldReceive('getLastVersion')->andThrow(ReleaseNotFoundException::class);

        $commandTester->execute([]);

        $this->assertContains('No existing version found', $commandTester->getDisplay());
        $this->assertSame(1, $commandTester->getStatusCode());
    }
}
