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
    public function testThatPullRequestTitlesAreReturned(): void
    {
        $vcs = Mockery::mock(VersionControlSystem::class);
        $generator = new PullRequestChangelogGenerator($vcs);

        $vcs->shouldReceive('getCommitsSinceLastVersion')
            ->andReturn([new Commit('Merge pull request #3 from branchname', 'Lorem ipsum')]);

        $changelog = $generator->getChangelog();

        $this->assertSame('Lorem ipsum (pull request #3)', $changelog->getChanges()[0]);
    }
}
