<?php
declare(strict_types=1);

namespace Leviy\ReleaseTool\Tests\Unit\Changelog;

use Leviy\ReleaseTool\Changelog\PullRequestChangelogGenerator;
use Leviy\ReleaseTool\Vcs\Commit;
use Leviy\ReleaseTool\Vcs\VersionControlSystem;
use Leviy\ReleaseTool\Versioning\SemanticVersion;
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

    public function testThatAChangelogOfUnreleasedChangesIsReturned(): void
    {
        $generator = new PullRequestChangelogGenerator($this->versionControlSystem);

        $this->versionControlSystem->shouldReceive('getCommitsSinceLastVersion')
            ->andReturn([new Commit('Merge pull request #3 from branchname', 'Lorem ipsum')]);

        $changelog = $generator->getUnreleasedChangelog();

        $this->assertContains('Lorem ipsum (pull request #3)', $changelog->getUnreleasedChanges());
    }

    public function testThatAChangelogWithChangesIntroducedInAVersionIsReturned(): void
    {
        $generator = new PullRequestChangelogGenerator($this->versionControlSystem);

        $this->versionControlSystem->shouldReceive('getPreReleasesForVersion')->andReturn([]);

        $this->versionControlSystem->shouldReceive('getCommitsForVersion')
            ->andReturn([new Commit('Merge pull request #3 from branchname', 'Lorem ipsum')]);

        $changelog = $generator->getChangelogForVersion(SemanticVersion::createFromVersionString('1.0.0'));

        $this->assertContains('Lorem ipsum (pull request #3)', $changelog->getChangesForVersion('1.0.0'));
    }

    public function testVersionChangelogIncludesChangesFromPreReleaseVersions(): void
    {
        $generator = new PullRequestChangelogGenerator($this->versionControlSystem);

        $this->versionControlSystem->shouldReceive('getPreReleasesForVersion')
            ->andReturn(['1.0.0-alpha.1', '1.0.0-beta.1']);

        $this->versionControlSystem->shouldReceive('getCommitsForVersion')
            ->with('1.0.0-alpha.1', Mockery::any())
            ->andReturn([new Commit('Merge pull request #1 from branchname', 'First PR')]);

        $this->versionControlSystem->shouldReceive('getCommitsForVersion')
            ->with('1.0.0-beta.1', Mockery::any())
            ->andReturn([new Commit('Merge pull request #3 from branchname', 'Lorem ipsum')]);

        $this->versionControlSystem->shouldReceive('getCommitsForVersion')
            ->with('1.0.0', Mockery::any())
            ->andReturn([new Commit('Merge pull request #5 from branchname', 'Foo bar')]);

        $changelog = $generator->getChangelogForVersion(SemanticVersion::createFromVersionString('1.0.0'));

        $this->assertSame(['1.0.0-alpha.1', '1.0.0-beta.1', '1.0.0'], $changelog->getVersions());

        $this->assertSame(['First PR (pull request #1)'], $changelog->getChangesForVersion('1.0.0-alpha.1'));
        $this->assertSame(['Lorem ipsum (pull request #3)'], $changelog->getChangesForVersion('1.0.0-beta.1'));
        $this->assertSame(['Foo bar (pull request #5)'], $changelog->getChangesForVersion('1.0.0'));
    }
}
