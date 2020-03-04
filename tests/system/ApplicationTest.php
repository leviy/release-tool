<?php
declare(strict_types=1);

namespace Leviy\ReleaseTool\Tests\System;

use Leviy\ReleaseTool\Vcs\Git;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\InputStream;
use Symfony\Component\Process\Process;
use function exec;
use const PHP_EOL;

class ApplicationTest extends TestCase
{
    protected function setUp(): void
    {
        Git::execute('init');
        Git::execute('remote add origin git@github.com:org/repo.git');
    }

    protected function tearDown(): void
    {
        exec('rm -rf $GIT_DIR');
    }

    public function testBootsWithoutErrors(): void
    {
        $process = new Process(['build/release-tool.phar']);
        $process->run();

        $this->assertTrue($process->isSuccessful(), 'The command returned a non-zero exit code.');
        $this->assertStringContainsString('Leviy Release Tool', $process->getOutput());
    }

    public function testAsksForConfirmationBeforeReleasingAVersion(): void
    {
        if (!Process::isPtySupported()) {
            $this->markTestSkipped('PTY is not supported on this operating system.');
        }

        $this->commitFile('README.md', 'Initial commit');

        $input = new InputStream();

        $process = new Process(['build/release-tool.phar', 'release', '--no-ansi', '1.0.0']);
        $process->setInput($input);
        $process->setPty(true);
        $process->start();

        // EOL simulates [Enter]
        // Do you want to continue? (yes/no)
        $input->write('no' . PHP_EOL);
        $input->close();

        $process->wait();

        $this->assertStringContainsString('This will release version 1.0.0', $process->getOutput());
        $this->assertStringContainsString('Do you want to continue?', $process->getOutput());
        $this->assertEmpty($this->getTags());
    }

    public function testReleasesWithGivenVersionNumber(): void
    {
        if (!Process::isPtySupported()) {
            $this->markTestSkipped('PTY is not supported on this operating system.');
        }

        $this->commitFile('README.md', 'Initial commit');

        $input = new InputStream();

        $process = new Process(['build/release-tool.phar', 'release', '1.0.0']);
        $process->setInput($input);
        $process->setPty(true);
        $process->start();

        // EOL simulates [Enter]
        // Do you want to continue? (yes/no)
        $input->write('yes' . PHP_EOL);

        // A VCS tag has been created for version 1.0.0. Do you want to push it to the remote repository and perform
        // additional release steps? (yes/no)
        $input->write('no' . PHP_EOL);
        $input->close();

        $process->wait();

        $this->assertTrue($process->isSuccessful(), 'The command returned a non-zero exit code.');
        $this->assertSame(['v1.0.0'], $this->getTags());
    }

    public function testShowsTheChangelogBeforeAskingInteractiveQuestions(): void
    {
        if (!Process::isPtySupported()) {
            $this->markTestSkipped('PTY is not supported on this operating system.');
        }

        $this->commitFile('README.md', 'Initial commit');
        $this->createTag('v1.0.0');
        $this->commitFile('phpunit.xml', 'Merge pull request #3 from branchname' . PHP_EOL . PHP_EOL . 'My PR title');

        $input = new InputStream();

        $process = new Process(['build/release-tool.phar', 'release']);
        $process->setInput($input);
        $process->setPty(true);
        $process->start();

        // EOL simulates [Enter]
        // Does this release contain backward incompatible changes? (yes/no)
        $input->write('no' . PHP_EOL);

        // Does this release contain new features? (yes/no)
        $input->write('yes' . PHP_EOL);

        // Do you want to continue? (yes/no)
        $input->write('no' . PHP_EOL);
        $input->close();

        $process->wait();

        $this->assertStringContainsString('My PR title (pull request #3)', $process->getOutput());
    }

    public function testDeterminesTheVersionNumberBasedOnInteractiveQuestions(): void
    {
        if (!Process::isPtySupported()) {
            $this->markTestSkipped('PTY is not supported on this operating system.');
        }

        $this->commitFile('README.md', 'Initial commit');
        $this->createTag('v1.0.0');
        $this->commitFile('phpunit.xml', 'Merge pull request #3 from branchname' . PHP_EOL . PHP_EOL . 'Foo');

        $input = new InputStream();

        $process = new Process(['build/release-tool.phar', 'release']);
        $process->setInput($input);
        $process->setPty(true);
        $process->start();

        // EOL simulates [Enter]
        // Does this release contain backward incompatible changes? (yes/no)
        $input->write('no' . PHP_EOL);

        // Does this release contain new features? (yes/no)
        $input->write('yes' . PHP_EOL);

        // Do you want to continue? (yes/no)
        $input->write('yes' . PHP_EOL);

        // A VCS tag has been created for version 1.0.0. Do you want to push it to the remote repository and perform
        // additional release steps? (yes/no)
        $input->write('no' . PHP_EOL);
        $input->close();

        $process->wait();

        $this->assertTrue($process->isSuccessful(), 'The command returned a non-zero exit code.');
        $this->assertContains('v1.1.0', $this->getTags());
    }

    public function testReturnsTheCurrentVersion(): void
    {
        $this->commitFile('README.md', 'Initial commit');
        $this->createTag('v1.2.5');

        $process = new Process(['build/release-tool.phar', 'current']);
        $process->run();

        $this->assertTrue($process->isSuccessful(), 'The command returned a non-zero exit code.');
        $this->assertStringContainsString('Current version: 1.2.5', $process->getOutput());
    }

    private function commitFile(string $filename, string $commitMessage): void
    {
        Git::execute('add ' . $filename);
        Git::execute('commit --no-gpg-sign -m "' . $commitMessage . '"');
    }

    private function createTag(string $tag): void
    {
        Git::execute('tag --annotate --message="Test tag" ' . $tag);
    }

    /**
     * @return string[]
     */
    private function getTags(): array
    {
        return Git::execute('tag');
    }
}
