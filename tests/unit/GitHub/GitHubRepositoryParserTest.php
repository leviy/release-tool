<?php
declare(strict_types=1);

namespace Leviy\ReleaseTool\Tests\Unit\GitHub;

use Leviy\ReleaseTool\GitHub\GitHubRepositoryParser;
use PHPUnit\Framework\TestCase;

class GitHubRepositoryParserTest extends TestCase
{
    /**
     * @dataProvider ownerProvider
     */
    public function testThatOwnerIsReturned(string $url, string $owner): void
    {
        $parser = new GitHubRepositoryParser();
        $actualOwner = $parser->getOwner($url);

        $this->assertSame($owner, $actualOwner);
    }

    /**
     * @return string[]
     */
    public function ownerProvider(): array
    {
        return [
            ['git@github.com:leviy/release-tool.git', 'leviy'],
            ['git@github.com:symfony/acme.git', 'symfony'],
        ];
    }

    /**
     * @dataProvider repositoryNameProvider
     */
    public function testThatRepositoryNameIsReturned(): void
    {
        $parser = new GitHubRepositoryParser();
        $repository = $parser->getRepository('git@github.com:leviy/release-tool.git');

        $this->assertSame('release-tool', $repository);
    }

    /**
     * @return string[]
     */
    public function repositoryNameProvider(): array
    {
        return [
            ['git@github.com:leviy/release-tool.git', 'release-tool'],
            ['git@github.com:symfony/acme.git', 'acme'],
        ];
    }
}
