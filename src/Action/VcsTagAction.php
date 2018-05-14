<?php
declare(strict_types=1);

namespace Leviy\ReleaseTool\Action;

use Leviy\ReleaseTool\Vcs\VersionControlSystem;

final class VcsTagAction implements Action
{
    /**
     * @var VersionControlSystem
     */
    private $versionControlSystem;

    /**
     * @var string
     */
    private $prefix;

    public function __construct(VersionControlSystem $versionControlSystem, string $prefix = '')
    {
        $this->versionControlSystem = $versionControlSystem;
        $this->prefix = $prefix;
    }

    public function release(string $version): void
    {
        $tag = $this->prefix . $version;

        $this->versionControlSystem->tag($tag);
    }
}
