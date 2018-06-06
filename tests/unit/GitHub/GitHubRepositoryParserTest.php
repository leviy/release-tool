<?php
declare(strict_types=1);

namespace Leviy\ReleaseTool\Tests\Unit\GitHub;

use Leviy\ReleaseTool\GitHub\GitHubRepositoryParser;
use Leviy\ReleaseTool\GitHub\InvalidUrlException;
use PHPUnit\Framework\TestCase;

class GitHubRepositoryParserTest extends TestCase
{
    /**
     * @dataProvider ownerProvider
     *
     * @param string $url
     * @param string $owner
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
     *
     * @param string $url
     * @param string $repository
     */
    public function testThatRepositoryNameIsReturned(string $url, string $repository): void
    {
        $parser = new GitHubRepositoryParser();
        $actualRepository = $parser->getRepository($url);

        $this->assertSame($repository, $actualRepository);
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

    public function testThatExceptionIsThrownForHttpUrl(): void
    {
        $this->expectException(InvalidUrlException::class);

        $parser = new GitHubRepositoryParser();
        $parser->getRepository('https://github.com/leviy/release-tool.git');
    }
}
