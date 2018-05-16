<?php
declare(strict_types=1);

namespace Leviy\ReleaseTool\Tests\Unit\Console\Command;

use Leviy\ReleaseTool\Console\Command\CurrentCommand;
use Leviy\ReleaseTool\Vcs\ReleaseNotFoundException;
use Leviy\ReleaseTool\Vcs\VersionControlSystem;
use Mockery;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\ApplicationTester;

class CurrentCommandTest extends TestCase
{
    /**
     * @var Mockery\MockInterface|VersionControlSystem
     */
    private $vcs;

    /**
     * @var ApplicationTester
     */
    private $console;

    protected function setUp(): void
    {
        $this->vcs = Mockery::mock(VersionControlSystem::class);

        $application = new Application();
        $application->add(new CurrentCommand($this->vcs));
        $application->setAutoExit(false);

        $this->console = new ApplicationTester($application);
    }

    public function testThatItOutputsTheVersionNumber(): void
    {
        $this->vcs->shouldReceive('getLastVersion')->andReturn('3.2.0');

        $this->runCommand('current');

        $this->assertContains('Current version: 3.2.0', $this->console->getDisplay());
        $this->assertSame(0, $this->console->getStatusCode());
    }

    public function testThatItShowsAnErrorMessageIfNoVersionIsFound(): void
    {
        $this->vcs->shouldReceive('getLastVersion')->andThrow(ReleaseNotFoundException::class);

        $this->runCommand('current');

        $this->assertContains('No existing version found', $this->console->getDisplay());
        $this->assertSame(1, $this->console->getStatusCode());
    }

    private function runCommand(string $command): void
    {
        $this->console->run(['command' => $command]);
    }
}
