<?php
declare(strict_types=1);

namespace Leviy\ReleaseTool\Tests\Integration\Vcs;

use Leviy\ReleaseTool\Vcs\Git;
use Leviy\ReleaseTool\Vcs\ReleaseNotFoundException;
use PHPUnit\Framework\TestCase;
use function exec;

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

    private function getTestGitInstance(string $prefix = ''): Git
    {
        return new Git(self::TEST_TAG_PREFIX . $prefix);
    }

    private function createTag(string $tag): void
    {
        $tag = self::TEST_TAG_PREFIX . $tag;

        exec('git tag ' . $tag);
    }

    private function deleteTestTags(): void
    {
        exec('git tag -d $(git tag | grep ' . self::TEST_TAG_PREFIX . ')');
    }
}
