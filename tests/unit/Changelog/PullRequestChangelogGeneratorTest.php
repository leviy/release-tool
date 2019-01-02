<?php
declare(strict_types=1);

namespace Leviy\ReleaseTool\Tests\Unit\Changelog;

use Leviy\ReleaseTool\Changelog\PullRequestChangelogGenerator;
use Leviy\ReleaseTool\Vcs\Commit;
use Leviy\ReleaseTool\Vcs\VersionControlSystem;
use Mockery;
use PHPUnit\Framework\TestCase;

class PullRequestChangelogGeneratorTest extends TestCase
{
    /**
     * @var Mockery\MockInterface|VersionControlSystem
     */
    private $versionControlSystem;

    protected function setUp(): void
    {
        $this->versionControlSystem = Mockery::mock(VersionControlSystem::class);
    }

    public function testThatAllVersionsAreAddedToTheChangelog(): void
    {
        $generator = new PullRequestChangelogGenerator($this->versionControlSystem);

        $this->versionControlSystem->shouldReceive('listVersions')->andReturn(['1.0.0', '1.1.0']);
        $this->versionControlSystem->shouldReceive('getCommitsForVersion')->andReturn([]);
        $this->versionControlSystem->shouldReceive('getCommitsSinceLastVersion')->andReturn([]);

        $changelog = $generator->getChangelog();

        $this->assertSame(['1.0.0', '1.1.0'], $changelog->getVersions());
    }

    public function testThatTheChangelogContainsChangesPerVersion(): void
    {
        $generator = new PullRequestChangelogGenerator($this->versionControlSystem);

        $this->versionControlSystem->shouldReceive('listVersions')->andReturn(['1.0.0']);
        $this->versionControlSystem->shouldReceive('getCommitsForVersion')
            ->andReturn([new Commit('Merge pull request #3 from branchname', 'Lorem ipsum')]);
        $this->versionControlSystem->shouldReceive('getCommitsSinceLastVersion')->andReturn([]);

        $changelog = $generator->getChangelog();
        $changes = $changelog->getChangesForVersion('1.0.0');

        $this->assertCount(1, $changes);
        $this->assertSame('Lorem ipsum (pull request #3)', $changes[0]);
    }

    public function testThatUnreleasedChangesAreAddedToTheChangelog(): void
    {
        $generator = new PullRequestChangelogGenerator($this->versionControlSystem);

        $this->versionControlSystem->shouldReceive('listVersions')->andReturn(['1.0.0', '1.1.0']);
        $this->versionControlSystem->shouldReceive('getCommitsForVersion')->andReturn([]);
        $this->versionControlSystem->shouldReceive('getCommitsSinceLastVersion')
            ->andReturn([new Commit('Merge pull request #3 from branchname', 'Lorem ipsum')]);

        $changelog = $generator->getChangelog();
        $changes = $changelog->getUnreleasedChanges();

        $this->assertCount(1, $changes);
        $this->assertSame('Lorem ipsum (pull request #3)', $changes[0]);
    }
}
