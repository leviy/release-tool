<?php
declare(strict_types=1);

namespace Leviy\ReleaseTool\Tests\System;

use Leviy\ReleaseTool\Console\Application;
use Leviy\ReleaseTool\Vcs\Git;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\ApplicationTester;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use function exec;

class ApplicationTest extends TestCase
{
    /**
     * @var ApplicationTester
     */
    private $applicationTester;

    protected function setUp(): void
    {
        Git::execute('init');
        Git::execute('remote add origin git@github.com:org/repo.git');

        $container = new ContainerBuilder();
        $application = new Application($container);

        $application->setAutoExit(false);

        $this->applicationTester = new ApplicationTester($application);
    }

    protected function tearDown(): void
    {
        exec('rm -rf $GIT_DIR');
    }

    public function testThatTheApplicationIsBooted(): void
    {
        $this->applicationTester->run([]);

        $this->assertOutputContains('Leviy Release Tool', $this->applicationTester);
    }

    public function testThatTheApplicationReturnsTheCurrentVersion(): void
    {
        Git::execute('add README.md');
        Git::execute('commit -m "Test commit"');
        Git::execute('tag --annotate --message="Test tag" v1.2.5');

        $this->applicationTester->run(['current']);

        $this->assertOutputContains('Current version:', $this->applicationTester);
    }

    private function assertOutputContains(string $text, ApplicationTester $applicationTester): void
    {
        $this->assertContains($text, $applicationTester->getDisplay());
    }
}
