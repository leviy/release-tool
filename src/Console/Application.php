<?php
declare(strict_types=1);

namespace Leviy\ReleaseTool\Console;

use Symfony\Component\Console\Application as SymfonyApplication;

final class Application extends SymfonyApplication
{
    private const NAME = 'Leviy Release Tool';

    private const VERSION = '@package_version@';

    public function __construct()
    {
        parent::__construct(self::NAME, self::VERSION);
    }
}
