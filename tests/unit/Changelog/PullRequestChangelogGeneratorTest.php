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

        $this->versionControlSystem->shouldReceive('getCommitsForVersion')
            ->andReturn([new Commit('Merge pull request #3 from branchname', 'Lorem ipsum')]);

        $changelog = $generator->getChangelogForVersion(SemanticVersion::createFromVersionString('1.0.0'));

        $this->assertContains('Lorem ipsum (pull request #3)', $changelog->getChangesForVersion('1.0.0'));
    }
}
