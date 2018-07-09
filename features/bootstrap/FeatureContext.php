<?php
declare(strict_types=1);

use Behat\Behat\Context\Context;
use Leviy\ReleaseTool\Changelog\ChangelogGenerator;
use Leviy\ReleaseTool\Interaction\InformationCollector;
use Leviy\ReleaseTool\ReleaseManager;
use Leviy\ReleaseTool\Vcs\VersionControlSystem;
use Leviy\ReleaseTool\Versioning\SemanticVersioning;
use Mockery\MockInterface;
use PHPUnit\Framework\Assert;

class FeatureContext implements Context
{
    /**
     * @var MockInterface|VersionControlSystem
     */
    private $versionControlSystem;

    /**
     * @var ReleaseManager
     */
    private $releaseManager;

    /**
     * @var string|null
     */
    private $nextVersion;

    /**
     * @var MockInterface|InformationCollector
     */
    private $informationCollector;

    public function __construct()
    {
        $this->informationCollector = Mockery::mock(InformationCollector::class);
        $this->versionControlSystem = Mockery::mock(VersionControlSystem::class);
        $this->versionControlSystem->shouldIgnoreMissing();

        $changelogGenerator = Mockery::mock(ChangelogGenerator::class);
        $changelogGenerator->shouldReceive('getChanges')->andReturn([]);

        $this->releaseManager = new ReleaseManager(
            $this->versionControlSystem,
            new SemanticVersioning(),
            $changelogGenerator,
            []
        );
    }

    /**
     * @Given a release on this branch with version :version
     */
    public function aReleaseOnThisBranchWithVersion(string $version): void
    {
        $this->versionControlSystem->shouldReceive('getLastVersion')->andReturn($version);
    }

    /**
     * @When I release a new :type version
     */
    public function iReleaseANewVersion(string $type): void
    {
        $this->selectVersionType($type);

        $this->nextVersion = $this->releaseManager->determineNextVersion($this->informationCollector);
    }

    /**
     * @When I release a(n) :preReleaseType version
     * @When /^I release a (release candidate)$/
     * @When I release a(n) :preReleaseType version of a new :type release
     */
    public function iReleaseAPreReleaseVersion(string $preReleaseType, ?string $type = null): void
    {
        if ($type !== null) {
            $this->selectVersionType($type);
        }

        switch ($preReleaseType) {
            case 'alpha':
                $answer = 'a';
                break;
            case 'beta':
                $answer = 'b';
                break;
            case 'release candidate':
                $answer = 'rc';
                break;
            default:
                return;
        }

        $this->informationCollector->shouldReceive('askMultipleChoice')->andReturn($answer);
        $this->nextVersion = $this->releaseManager->determineNextPreReleaseVersion($this->informationCollector);
    }

    /**
     * @Then version :version should be released
     */
    public function versionShouldBeReleased(string $version): void
    {
        Assert::assertSame($version, $this->nextVersion);
    }

    private function selectVersionType(string $type): void
    {
        switch ($type) {
            case 'major':
                $answers = [true];
                break;
            case 'minor':
                $answers = [false, true];
                break;
            case 'patch':
                $answers = [false, false];
                break;
            default:
                return;
        }

        $this->informationCollector->shouldReceive('askConfirmation')->andReturn(...$answers);
    }
}
