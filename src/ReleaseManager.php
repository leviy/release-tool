<?php
declare(strict_types=1);

namespace Leviy\ReleaseTool;

use Assert\Assertion;
use Leviy\ReleaseTool\Action\Action;
use function array_walk;

final class ReleaseManager
{
    /**
     * @var Action[]
     */
    private $actions;

    /**
     * @param Action[] $actions
     */
    public function __construct(array $actions)
    {
        Assertion::allIsInstanceOf($actions, Action::class);

        $this->actions = $actions;
    }

    public function release(string $version): void
    {
        array_walk(
            $this->actions,
            function (Action $action) use ($version): void {
                $action->release($version);
            }
        );
    }
}
