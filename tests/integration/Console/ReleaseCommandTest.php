<?php
declare(strict_types=1);

namespace Leviy\ReleaseTool\Tests\Integration\Console;

use Leviy\ReleaseTool\Changelog\ChangelogGenerator;
use Leviy\ReleaseTool\Changelog\PullRequestChangelogGenerator;
use Leviy\ReleaseTool\Console\Command\ReleaseCommand;
use Leviy\ReleaseTool\ReleaseManager;
use Leviy\ReleaseTool\Vcs\Git;
use Leviy\ReleaseTool\Vcs\VersionControlSystem;
use Leviy\ReleaseTool\Versioning\SemanticVersioning;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;
use function exec;

class ReleaseCommandTest extends TestCase
{
    /**
     * @var VersionControlSystem
     */
    private $versionControlSystem;

    /**
     * @var ReleaseManager
     */
    private $releaseManager;

    /**
     * @var ChangelogGenerator
     */
    private $changelogGenerator;

    protected function setUp(): void
    {
        $this->versionControlSystem = new Git('v');
        $this->changelogGenerator = new PullRequestChangelogGenerator($this->versionControlSystem);

        $this->releaseManager = new ReleaseManager(
            $this->versionControlSystem,
            new SemanticVersioning(),
            $this->changelogGenerator,
            []
        );

        $this->removeGitDirectory();
        Git::execute('init');
        $this->commitFile('README.md', 'Merge pull request #3 from branch');
    }

    protected function tearDown(): void
    {
        $this->removeGitDirectory();
    }

    public function testThatItTagsANewVersion(): void
    {
        $command = new ReleaseCommand(
            $this->releaseManager,
            new PullRequestChangelogGenerator($this->versionControlSystem)
        );

        $commandTester = new CommandTester($command);

        $commandTester->setInputs(['yes', 'no']);
        $commandTester->execute(['version' => '1.0.0']);

        $this->assertContains('v1.0.0', $this->getTags());
    }

    public function testThatItAbortsTheReleaseOnNegativeConfirmation(): void
    {
        $command = new ReleaseCommand(
            $this->releaseManager,
            new PullRequestChangelogGenerator($this->versionControlSystem)
        );

        $commandTester = new CommandTester($command);

        $commandTester->setInputs(['no']);
        $commandTester->execute(['version' => '1.0.0']);

        $this->assertNotContains('v1.0.0', $this->getTags());
    }

    public function testThatItAsksInteractiveQuestionsToDetermineTheNextVersion(): void
    {
        Git::execute(
            'tag',
            [
                '--annotate',
                '--message="Test tag"',
                'v1.0.0',
            ]
        );

        $command = new ReleaseCommand(
            $this->releaseManager,
            new PullRequestChangelogGenerator($this->versionControlSystem)
        );

        $commandTester = new CommandTester($command);
        $commandTester->setInputs(['no', 'yes', 'yes', 'no']);

        $commandTester->execute([]);

        $this->assertContains('v1.1.0', $this->getTags());
    }

    private function commitFile(string $filename, string $commitMessage = 'Commit message'): void
    {
        Git::execute('add ' . $filename);
        Git::execute('commit --no-gpg-sign -m "' . $commitMessage . '"');
    }

    private function removeGitDirectory(): void
    {
        exec('rm -rf $GIT_DIR');
    }

    /**
     * @return string[]
     */
    private function getTags(): array
    {
        return Git::execute('tag');
    }
}
