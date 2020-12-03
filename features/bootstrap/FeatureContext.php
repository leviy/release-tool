<?php
declare(strict_types=1);

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use Leviy\ReleaseTool\Changelog\Formatter\MarkdownFormatter;
use Leviy\ReleaseTool\Changelog\PullRequestChangelogGenerator;
use Leviy\ReleaseTool\GitHub\GitHubClient;
use Leviy\ReleaseTool\Interaction\InformationCollector;
use Leviy\ReleaseTool\ReleaseAction\GitHubReleaseAction;
use Leviy\ReleaseTool\ReleaseManager;
use Leviy\ReleaseTool\Vcs\Commit;
use Leviy\ReleaseTool\Vcs\Git;
use Leviy\ReleaseTool\Vcs\VersionControlSystem;
use Leviy\ReleaseTool\Versioning\SemanticVersioning;
use Mockery\MockInterface;

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
     * @var MockInterface|InformationCollector
     */
    private $informationCollector;

    /**
     * @var MockInterface|GitHubClient
     */
    private $githubClient;

    /**
     * @var Commit[]
     */
    private $mergedPullRequests = [];

    public function __construct()
    {
        $this->informationCollector = Mockery::mock(InformationCollector::class);

        $this->versionControlSystem = Mockery::mock(Git::class);
        $this->versionControlSystem->shouldIgnoreMissing();

        $changelogGenerator = new PullRequestChangelogGenerator($this->versionControlSystem);

        $this->githubClient = Mockery::spy(GitHubClient::class);

        $githubReleaseAction = new GitHubReleaseAction(
            $changelogGenerator,
            new MarkdownFormatter([]),
            $this->versionControlSystem,
            $this->githubClient
        );

        $this->releaseManager = new ReleaseManager(
            $this->versionControlSystem,
            new SemanticVersioning(),
            [$githubReleaseAction]
        );
    }

    /**
     * @Given the latest release on this branch is :version
     */
    public function aReleaseOnThisBranchWithVersion(string $version): void
    {
        $this->versionControlSystem->shouldReceive('findLastVersion')->andReturn(true);
        $this->versionControlSystem->shouldReceive('getLastVersion')->andReturn($version);
    }

    /**
     * @When I release a new version
     * @When I release a new :type version
     */
    public function iReleaseANewVersion(?string $type = null): void
    {
        $this->selectVersionType($type);

        $version = $this->releaseManager->determineNextVersion($this->informationCollector);
        $this->releaseManager->release($version, $this->informationCollector);
    }

    /**
     * @When I release version :version
     */
    public function iReleaseVersion(string $version): void
    {
        $this->versionControlSystem->shouldReceive('getCommitsForVersion')
            ->with($version, Mockery::any())
            ->andReturn($this->mergedPullRequests);

        $this->versionControlSystem->shouldReceive('getTagForVersion')->andReturn($version);
        $this->informationCollector->shouldReceive('askConfirmation')->andReturnTrue();
        $this->releaseManager->release($version, $this->informationCollector);
    }

    /**
     * @When /^I release an? (release candidate|alpha|beta)(?: version)?$/
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

        $this->informationCollector->shouldReceive('askConfirmation')->andReturnTrue();
        $this->informationCollector->shouldReceive('askMultipleChoice')->andReturn($answer);

        $version = $this->releaseManager->determineNextPreReleaseVersion($this->informationCollector);
        $this->releaseManager->release($version, $this->informationCollector);
    }

    /**
     * @Then version :version should be released
     */
    public function versionShouldBeReleased(string $version): void
    {
        $this->versionControlSystem->shouldHaveReceived('createVersion', [$version]);
    }

    /**
     * @Given the pre-release :version was created
     */
    public function wasAPreRelease(string $version): void
    {
        $this->versionControlSystem->shouldReceive('getPreReleasesForVersion')->andReturn([$version]);
        $this->versionControlSystem->shouldReceive('getCommitsForVersion')
            ->with($version, Mockery::any())
            ->andReturn($this->mergedPullRequests);

        $this->mergedPullRequests = [];
    }

    /**
     * @Then a release with title :title should be published on GitHub with the following release notes:
     */
    public function aReleaseWithTitleShouldBePublishedOnGitHubWithTheFollowingReleaseNotes(
        string $version,
        PyStringNode $releaseNotes
    ) {
        $this->githubClient->shouldHaveReceived('createRelease', [Mockery::any(), $version, $releaseNotes->getRaw()]);
    }

    /**
     * @Given pull request :title with number :number was merged
     */
    public function pullRequestWasMerged(string $title, string $number)
    {
        $this->mergedPullRequests[] = new Commit('Merge pull request #' . $number . ' from branch', $title);
    }

    private function selectVersionType(?string $type): void
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
                $answers = [];
                break;
        }

        // Always respond positive to the question "Do you want to push it to the remote repository and perform
        // additional release steps?"
        $answers[] = true;

        $this->informationCollector->shouldReceive('askConfirmation')->andReturn(...$answers);
    }
}
