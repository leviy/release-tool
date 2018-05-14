<?php
declare(strict_types=1);

namespace Leviy\ReleaseTool\Tests\Unit\Action;

use Leviy\ReleaseTool\Action\VcsTagAction;
use Leviy\ReleaseTool\Vcs\VersionControlSystem;
use Mockery;
use PHPUnit\Framework\TestCase;

class VcsTagActionTest extends TestCase
{
    use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

    /**
     * @var Mockery\MockInterface|VersionControlSystem
     */
    private $versionControlSystem;

    protected function setUp(): void
    {
        $this->versionControlSystem = Mockery::spy(VersionControlSystem::class);
    }

    public function testThatTheVersionIsTagged(): void
    {
        $action = new VcsTagAction($this->versionControlSystem);

        $action->release('1.0.0');

        $this->versionControlSystem->shouldHaveReceived('tag', ['1.0.0']);
    }

    public function testThatTheTagPrefixIsAddedBeforeTheVersionNumber(): void
    {
        $action = new VcsTagAction($this->versionControlSystem, 'v');

        $action->release('1.0.0');

        $this->versionControlSystem->shouldHaveReceived('tag', ['v1.0.0']);
    }
}
