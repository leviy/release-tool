<?php
declare(strict_types=1);

namespace Leviy\ReleaseTool\Tests\Unit\Console\Command;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\ApplicationTester;
use function array_merge;

abstract class CommandTest extends TestCase
{
    /**
     * @var ApplicationTester
     */
    protected $console;

    protected function setUpApplicationTester(Command $command): void
    {
        $application = new Application();
        $application->add($command);
        $application->setAutoExit(false);

        $this->console = new ApplicationTester($application);
    }

    /**
     * @param string   $command
     * @param string[] $arguments
     *
     * @return void
     */
    protected function runCommand(string $command, array $arguments = []): void
    {
        $this->console->run(array_merge(['command' => $command], $arguments));
    }
}
