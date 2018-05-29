<?php
declare(strict_types=1);

namespace Leviy\ReleaseTool\Tests\Integration\Vcs;

use Leviy\ReleaseTool\Vcs\Git;
use Leviy\ReleaseTool\Vcs\ReleaseNotFoundException;
use PHPUnit\Framework\TestCase;
use function exec;

class GitTest extends TestCase
{
    protected function setUp(): void
    {
        Git::execute('init');
        Git::execute('add README.md');
        Git::execute('commit -m "First commit"');
    }

    protected function tearDown(): void
    {
        exec('rm -rf $GIT_DIR');
    }

    public function testThatTheLastVersionIsReturned(): void
    {
        $this->createTag('1.0.0');

        $git = new Git();

        $this->assertSame('1.0.0', $git->getLastVersion());
    }

    public function testThatTheTagPrefixIsStrippedFromTheTag(): void
    {
        $this->createTag('v1.0.0');

        $git = new Git('v');

        $this->assertSame('1.0.0', $git->getLastVersion());
    }

    public function testThatNoCommitHashAndNumberOfAdditionalCommitsAreReturned(): void
    {
        Git::execute('add phpunit.xml');
        Git::execute('commit -m "Another commit"');

        $this->createTag('1.0.0', 'HEAD^');

        $git = new Git();

        $this->assertSame('1.0.0', $git->getLastVersion());
    }

    public function testThatAnExceptionIsThrownIfNoMatchingTagIsFound(): void
    {
        $this->expectException(ReleaseNotFoundException::class);

        $git = new Git();

        $git->getLastVersion();
    }

    public function testThatNonPrefixedTagsAreIgnored(): void
    {
        $this->createTag('foo');

        $this->expectException(ReleaseNotFoundException::class);

        $git = new Git();

        $git->getLastVersion();
    }

    public function testThatANewVersionIsTagged(): void
    {
        $git = new Git('v');

        $git->createVersion('1.2.0');

        $this->assertContains('v1.2.0', $this->getTags());
    }

    /**
     * @return string[]
     */
    private function getTags(): array
    {
        exec('git tag', $output);

        return $output;
    }

    private function createTag(string $tag, ?string $head = null): void
    {
        if ($head !== null) {
            exec('git tag --annotate --message="Test tag" ' . $tag . ' ' . $head);

            return;
        }

        exec('git tag --annotate --message="Test tag" ' . $tag);
    }
}
