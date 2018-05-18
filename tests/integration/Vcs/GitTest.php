<?php
declare(strict_types=1);

namespace Leviy\ReleaseTool\Tests\Integration\Vcs;

use Leviy\ReleaseTool\Vcs\Git;
use Leviy\ReleaseTool\Vcs\ReleaseNotFoundException;
use PHPUnit\Framework\TestCase;
use function array_map;
use function exec;
use function str_replace;

class GitTest extends TestCase
{
    /**
     * Prefix all tags with "test-" to avoid messing with actual tags of the release tool!
     */
    private const TEST_TAG_PREFIX = 'test-';

    protected function tearDown(): void
    {
        $this->deleteTestTags();
    }

    public function testThatTheLastVersionIsReturned(): void
    {
        $this->createTag('1.0.0');

        $git = $this->getTestGitInstance();

        $this->assertSame('1.0.0', $git->getLastVersion());
    }

    public function testThatTheTagPrefixIsStrippedFromTheTag(): void
    {
        $this->createTag('v1.0.0');

        $git = $this->getTestGitInstance('v');

        $this->assertSame('1.0.0', $git->getLastVersion());
    }

    public function testThatNoCommitHashAndNumberOfAdditionalCommitsAreReturned(): void
    {
        $this->createTag('1.0.0', 'HEAD~2');

        $git = $this->getTestGitInstance();

        $this->assertSame('1.0.0', $git->getLastVersion());
    }

    public function testThatAnExceptionIsThrownIfNoMatchingTagIsFound(): void
    {
        $this->expectException(ReleaseNotFoundException::class);

        $git = $this->getTestGitInstance();

        $git->getLastVersion();
    }

    public function testThatNonPrefixedTagsAreIgnored(): void
    {
        $this->createTag('foo');

        $this->expectException(ReleaseNotFoundException::class);

        $git = $this->getTestGitInstance();

        $git->getLastVersion();
    }

    public function testThatANewVersionIsTagged(): void
    {
        $git = $this->getTestGitInstance('v');

        $git->createVersion('1.2.0');

        $this->assertContains('v1.2.0', $this->getTags());
    }

    private function getTestGitInstance(string $prefix = ''): Git
    {
        return new Git(self::TEST_TAG_PREFIX . $prefix);
    }

    /**
     * @return string[]
     */
    private function getTags(): array
    {
        exec('git tag', $output);

        return array_map(
            function (string $tag): string {
                return str_replace(self::TEST_TAG_PREFIX, '', $tag);
            },
            $output
        );
    }

    private function createTag(string $tag, ?string $head = null): void
    {
        $tag = self::TEST_TAG_PREFIX . $tag;

        if ($head !== null) {
            exec('git tag --annotate --message="Test tag" ' . $tag . ' ' . $head);

            return;
        }

        exec('git tag --annotate --message="Test tag" ' . $tag);
    }

    private function deleteTestTags(): void
    {
        exec('git tag -d $(git tag | grep ' . self::TEST_TAG_PREFIX . ')');
    }
}
