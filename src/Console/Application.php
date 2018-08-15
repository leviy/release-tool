<?php
declare(strict_types=1);

namespace Leviy\ReleaseTool\Console;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Console\Application as SymfonyApplication;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

final class Application extends SymfonyApplication
{
    private const NAME = 'Leviy Release Tool';

    private const VERSION = '@package_version@';

    public function __construct(ContainerBuilder $container)
    {
        parent::__construct(self::NAME, self::VERSION);

        $this->buildContainer($container);
    }

    private function buildContainer(ContainerBuilder $container): void
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../../config'));
        $loader->load('services.yaml');
        $loader->load('github.yaml');
        $loader->load('commands.yaml');
    }
}
