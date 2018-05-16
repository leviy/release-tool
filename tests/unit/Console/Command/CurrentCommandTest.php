<?php
declare(strict_types=1);

namespace Leviy\ReleaseTool\Tests\Unit\Console\Command;

use Leviy\ReleaseTool\Console\Command\CurrentCommand;
use Leviy\ReleaseTool\Vcs\VersionControlSystem;
use Mockery;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use function array_merge;

class CurrentCommandTest extends TestCase
{
    /**
     * @var Mockery\MockInterface|VersionControlSystem
     */
    private $vcs;

    /**
     * @var Application
     */
    private $application;

    protected function setUp(): void
    {
        $this->application = new Application();
        $this->vcs = Mockery::mock(VersionControlSystem::class);
    }

    public function testThatItOutputsTheVersionNumber(): void
    {
        $this->application->add(new CurrentCommand($this->vcs));

        $this->vcs->shouldReceive('getLastVersion')->andReturn('3.2.0');

        $output = $this->runCommand('current');

        $this->assertContains('Current version: 3.2.0', $output);
    }

    /**
     * @param string   $command
     * @param string[] $arguments
     *
     * @return string
     */
    private function runCommand(string $command, array $arguments = []): string
    {
        $commandTester = new CommandTester($this->application->find($command));
        $commandTester->execute(array_merge(['command' => $command], $arguments));

        return $commandTester->getDisplay();
    }
}
