<?php
declare(strict_types=1);

namespace Leviy\ReleaseTool\Vcs;

final class Commit
{
    /**
     * @var string
     */
    public $title;

    /**
     * @var string
     */
    public $body;

    public function __construct(string $title, string $body = '')
    {
        $this->title = $title;
        $this->body = $body;
    }
}
