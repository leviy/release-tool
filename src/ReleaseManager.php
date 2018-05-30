<?php
declare(strict_types=1);

namespace Leviy\ReleaseTool;

use Leviy\ReleaseTool\Interaction\InformationCollector;
use Leviy\ReleaseTool\Vcs\VersionControlSystem;
use Leviy\ReleaseTool\Versioning\Strategy;

class ReleaseManager
{
    /**
     * @var VersionControlSystem
     */
    private $versionControlSystem;

    /**
     * @var Strategy
     */
    private $versioningStrategy;

    public function __construct(
        VersionControlSystem $versionControlSystem,
        Strategy $versioningStrategy
    ) {
        $this->versionControlSystem = $versionControlSystem;
        $this->versioningStrategy = $versioningStrategy;
    }

    public function getCurrentVersion(): string
    {
        return $this->versionControlSystem->getLastVersion();
    }

    public function release(string $version, InformationCollector $informationCollector): void
    {
        $this->versionControlSystem->createVersion($version);

        if (!$informationCollector->askConfirmation('Do you want to push version ' . $version . ' to remote?')) {
            return;
        }

        $this->versionControlSystem->pushVersion($version);
    }

    public function determineNextVersion(InformationCollector $informationCollector): string
    {
        $current = $this->versionControlSystem->getLastVersion();

        return $this->versioningStrategy->getNextVersion($current, $informationCollector);
    }
}
