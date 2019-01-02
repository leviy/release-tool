<?php
declare(strict_types=1);

namespace Leviy\ReleaseTool\Tests\Unit\Console\Command;

use InvalidArgumentException;
use Leviy\ReleaseTool\Changelog\Changelog;
use Leviy\ReleaseTool\Changelog\ChangelogGenerator;
use Leviy\ReleaseTool\Console\Command\ReleaseCommand;
use Leviy\ReleaseTool\ReleaseManager;
use Leviy\ReleaseTool\Vcs\VersionControlSystem;
use Leviy\ReleaseTool\Versioning\VersioningScheme;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

class ReleaseCommandTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @var MockInterface|VersionControlSystem
     */
    private $vcs;

    /**
     * @var MockInterface|VersioningScheme
     */
    private $versioningStrategy;

    /**
     * @var MockInterface|ChangelogGenerator
     */
    private $changelogGenerator;

    /**
     * @var MockInterface|ReleaseManager
     */
    private $releaseManager;

    protected function setUp(): void
    {
        $this->vcs = Mockery::spy(VersionControlSystem::class);
        $this->versioningStrategy = Mockery::mock(VersioningScheme::class);
        $this->changelogGenerator = Mockery::mock(ChangelogGenerator::class);

        $this->releaseManager = Mockery::spy(ReleaseManager::class);
    }

    public function testThatItFailsIfTheProvidedVersionIsInvalid(): void
    {
        $command = new ReleaseCommand($this->releaseManager, $this->changelogGenerator);
        $this->changelogGenerator->shouldReceive('getChangelog');
        $this->releaseManager->shouldReceive('isValidVersion')->andReturnFalse();

        $this->expectException(InvalidArgumentException::class);

        $commandTester = new CommandTester($command);

        $commandTester->setInputs(['yes']);
        $commandTester->execute(['version' => '1.2.0']);
    }

    public function testThatItReleasesANewVersion(): void
    {
        $command = new ReleaseCommand($this->releaseManager, $this->changelogGenerator);
        $this->changelogGenerator->shouldReceive('getChangelog');
        $this->releaseManager->shouldReceive('isValidVersion')->andReturnTrue();

        $commandTester = new CommandTester($command);

        $commandTester->setInputs(['yes']);
        $commandTester->execute(['version' => '1.2.0']);

        $this->releaseManager->shouldHaveReceived('release', ['1.2.0', Mockery::any()]);
    }

    public function testThatItAbortsTheReleaseOnNegativeConfirmation(): void
    {
        $command = new ReleaseCommand($this->releaseManager, $this->changelogGenerator);
        $this->changelogGenerator->shouldReceive('getChangelog');
        $this->releaseManager->shouldReceive('isValidVersion')->andReturnTrue();

        $commandTester = new CommandTester($command);

        $commandTester->setInputs(['no']);
        $commandTester->execute(['version' => '1.2.0']);

        $this->releaseManager->shouldNotHaveReceived('release');
    }

    public function testThatItUsesTheReleaseManagerToDetermineTheNextVersion(): void
    {
        $command = new ReleaseCommand($this->releaseManager, $this->changelogGenerator);
        $this->changelogGenerator->shouldReceive('getChangelog')->andReturn(new Changelog());
        $this->releaseManager->shouldReceive('isValidVersion')->andReturnTrue();

        $this->releaseManager->shouldReceive('determineNextVersion')->andReturn('2.3.0');

        $commandTester = new CommandTester($command);
        $commandTester->setInputs(['yes']);

        $commandTester->execute([]);

        $this->releaseManager->shouldHaveReceived('release', ['2.3.0', Mockery::any()]);
    }
}
