<?php
declare(strict_types=1);

namespace Leviy\ReleaseTool\Tests\Unit\Console\Command;

use Leviy\ReleaseTool\Console\Command\ReleaseCommand;
use Leviy\ReleaseTool\Vcs\VersionControlSystem;
use Mockery;
use Mockery\MockInterface;

class ReleaseCommandTest extends CommandTest
{
    use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

    /**
     * @var MockInterface|VersionControlSystem
     */
    private $vcs;

    protected function setUp(): void
    {
        $this->vcs = Mockery::spy(VersionControlSystem::class);

        parent::setUpApplicationTester(new ReleaseCommand($this->vcs));
    }

    public function testThatItCreatesANewRelease(): void
    {
        $this->runCommand('release', ['version' => '1.2.0']);

        $this->vcs->shouldHaveReceived('createVersion');
    }
}
