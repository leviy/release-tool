<?php
declare(strict_types=1);

namespace Leviy\ReleaseTool\Tests\Unit\Vcs;

use Leviy\ReleaseTool\Vcs\Git;
use Leviy\ReleaseTool\Vcs\RepositoryNotFoundException;
use PHPUnit\Framework\TestCase;

class GitTest extends TestCase
{
    public function testThatAnExceptionIsThrownIfNoGitDirectoryIsPresent(): void
    {
        $this->expectException(RepositoryNotFoundException::class);

        Git::execute('status');
    }
}
